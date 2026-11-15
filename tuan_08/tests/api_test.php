<?php

// Kiểm tra API đang chạy; chỉ tạo và xóa các sản phẩm do bài kiểm tra tạo ra.
$url = $argv[1] ?? 'http://127.0.0.1:8060/tuan_08/api/products.php';
$createdIds = [];
$checks = 0;

function api(string $method, ?array $data = null, ?int $id = null, ?string $raw = null): array
{
    global $url;
    $curl = curl_init($url . ($id === null ? '' : '?id=' . $id));
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 10,
    ]);
    if ($data !== null || $raw !== null) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $raw ?? json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    $body = curl_exec($curl);
    $error = curl_error($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($body === false) throw new RuntimeException($error);
    $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($json) || !array_key_exists('success', $json)) throw new RuntimeException('Phản hồi không đúng cấu trúc.');
    return ['status' => $status, 'body' => $json];
}

function check(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}

try {
    $initial = api('GET');
    check($initial['status'] === 200 && is_array($initial['body']['data']), 'GET danh sách thất bại.');
    $sample = ['name' => '  Sản phẩm kiểm tra ' . uniqid() . '  ', 'price' => 5000, 'stock' => 2];
    $added = api('POST', $sample);
    check($added['status'] === 201 && $added['body']['success'], 'Thêm sản phẩm thất bại.');
    $id = $added['body']['data']['id'];
    $createdIds[] = $id;
    $stored = array_column(api('GET')['body']['data'], null, 'id');
    check(isset($stored[$id]) && $stored[$id]['name'] === trim($sample['name']), 'Sản phẩm không được lưu hoặc tên chưa trim.');

    $sample['price'] = 9000;
    check(api('PUT', $sample, $id)['body']['success'], 'Cập nhật thất bại.');
    $stored = array_column(api('GET')['body']['data'], null, 'id');
    check($stored[$id]['price'] === 9000, 'Đơn giá mới không được lưu.');
    check(api('PUT', $sample, $id)['body']['success'], 'Lưu giá trị không đổi bị báo lỗi.');

    $before = api('GET')['body']['data'];
    $invalidCases = [
        ['name' => 'Sai tồn kho', 'price' => 5000, 'stock' => -1],
        ['name' => '   ', 'price' => 5000, 'stock' => 0],
        ['name' => str_repeat('á', 101), 'price' => 5000, 'stock' => 0],
        ['name' => 'Sai giá', 'price' => 0, 'stock' => 0],
        ['name' => 'Giá lẻ', 'price' => 1.5, 'stock' => 0],
        ['name' => 'Giá chuỗi', 'price' => '5000', 'stock' => 0],
        ['name' => 'Tồn kho lẻ', 'price' => 5000, 'stock' => 0.5],
        ['name' => 'Vượt INT', 'price' => 2147483648, 'stock' => 0],
    ];
    foreach ($invalidCases as $invalid) {
        $response = api('POST', $invalid);
        check($response['status'] === 422 && !$response['body']['success'] && isset($response['body']['message']), 'API chấp nhận dữ liệu sai.');
    }
    check(api('GET')['body']['data'] === $before, 'Dữ liệu thay đổi sau yêu cầu không hợp lệ.');
    $invalidUpdate = api('PUT', ['name' => 'Sai', 'price' => 5000, 'stock' => -1], $id);
    check($invalidUpdate['status'] === 422 && api('GET')['body']['data'] === $before, 'PUT sai vẫn thay đổi dữ liệu.');
    check(api('POST', null, null, '{')['status'] === 400, 'JSON hỏng không bị từ chối.');
    check(api('POST', null, null, '[]')['status'] === 400, 'Chấp nhận JSON mảng.');
    check(api('PATCH')['status'] === 405, 'Phương thức sai không bị từ chối.');
    check(api('DELETE', null, 0)['status'] === 400, 'Mã 0 không bị từ chối.');

    $unicode = api('POST', ['name' => str_repeat('á', 100), 'price' => 1, 'stock' => 0]);
    check($unicode['status'] === 201, 'Không chấp nhận tên tiếng Việt 100 ký tự hoặc tồn kho 0.');
    $createdIds[] = $unicode['body']['data']['id'];

    check(api('DELETE', null, $id)['body']['success'], 'Xóa thất bại.');
    $createdIds = array_values(array_diff($createdIds, [$id]));
    $stored = array_column(api('GET')['body']['data'], null, 'id');
    check(!isset($stored[$id]), 'Sản phẩm đã xóa vẫn xuất hiện.');
    check(api('PUT', $sample, $id)['status'] === 404, 'Sửa mã không tồn tại chưa báo lỗi.');
    check(api('DELETE', null, $id)['status'] === 404, 'Xóa mã không tồn tại chưa báo lỗi.');
    echo "PASS: $checks kiểm tra API.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . "\n");
    $failed = true;
} finally {
    foreach ($createdIds as $id) {
        try {
            api('DELETE', null, $id);
        } catch (Throwable $exception) {
            fwrite(STDERR, "Chưa dọn được sản phẩm kiểm tra #$id.\n");
        }
    }
}
exit(isset($failed) ? 1 : 0);
