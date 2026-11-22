<?php

// Chỉ chạy với API trỏ vào database kiểm tra RIÊNG và đang rỗng.
$base = rtrim($argv[1] ?? 'http://127.0.0.1:8061/tuan_09', '/');
$ids = [];
$temporaryFiles = [];
$checks = 0;
$failed = false;

function requestApi(string $method, string $path, $body = null): array
{
    global $base;
    $curl = curl_init($base . '/api/' . $path);
    $headers = ['Accept: application/json'];
    if (is_array($body) && !isset($body['image'])) {
        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        $headers[] = 'Content-Type: application/json';
    }
    curl_setopt_array($curl, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 10,
    ]);
    if ($body !== null) curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    $result = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);
    if ($result === false) throw new RuntimeException($error);
    $data = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($data) || !array_key_exists('success', $data)) throw new RuntimeException('Phản hồi không đúng cấu trúc.');
    return ['status' => $status, 'body' => $data];
}

function check(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}

function upload(int $id, string $file, string $mime = 'image/png', string $name = 'anh.png'): array
{
    return requestApi('POST', 'upload.php', ['product_id' => (string) $id, 'image' => new CURLFile($file, $mime, $name)]);
}

function currentImage(int $id): ?string
{
    $last = requestApi('GET', 'products.php')['body']['pagination']['total_pages'];
    for ($page = 1; $page <= $last; $page++) {
        foreach (requestApi('GET', 'products.php?page=' . $page)['body']['data'] as $product) {
            if ($product['id'] === $id) return $product['image'];
        }
    }
    throw new RuntimeException('Không tìm thấy sản phẩm kiểm tra.');
}

try {
    $empty = requestApi('GET', 'products.php')['body'];
    check($empty['pagination']['total'] === 0, 'Phải dùng database kiểm tra riêng, đang rỗng. Không chạy trên dữ liệu bài tập.');
    check($empty['data'] === [] && $empty['pagination']['page'] === 1 && $empty['pagination']['total_pages'] === 1, 'Sai phân trang khi rỗng.');
    for ($i = 1; $i <= 12; $i++) {
        $added = requestApi('POST', 'products.php', ['name' => 'Mẫu kiểm tra ' . $i, 'price' => 5000 + $i, 'stock' => $i]);
        check($added['status'] === 201 && isset($added['body']['data']['id']), 'POST không trả mã sản phẩm.');
        $ids[] = $added['body']['data']['id'];
    }
    $allIds = [];
    foreach ([1 => 5, 2 => 5, 3 => 2] as $page => $count) {
        $data = requestApi('GET', 'products.php?page=' . $page . '&limit=5')['body'];
        check(count($data['data']) === $count && $data['pagination']['total_pages'] === 3 && $data['pagination']['total'] === 12, 'Phân trang 12 sản phẩm không đúng 5/5/2.');
        $allIds = array_merge($allIds, array_column($data['data'], 'id'));
    }
    check($allIds === array_reverse($ids), 'Sản phẩm không sắp xếp theo id giảm dần hoặc trang bị trùng.');
    $over = requestApi('GET', 'products.php?page=999')['body'];
    check($over['pagination']['page'] === 3 && count($over['data']) === 2, 'Không trả trang cuối khi vượt giới hạn.');
    foreach (['page=0', 'page=-1', 'page=1.5', 'page=abc', 'page=', 'page[]=1', 'limit=0', 'limit=10', 'limit=5.5', 'limit=abc'] as $query) {
        $response = requestApi('GET', 'products.php?' . $query);
        check($response['status'] === 400 && !$response['body']['success'], 'API chấp nhận tham số sai: ' . $query);
    }

    $png = tempnam(sys_get_temp_dir(), 'vue9_');
    $temporaryFiles[] = $png;
    file_put_contents($png, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aRZkAAAAASUVORK5CYII='));
    $targetId = $ids[11];
    $first = upload($targetId, $png);
    check($first['status'] === 200 && $first['body']['success'], 'Upload PNG hợp lệ thất bại.');
    $path1 = $first['body']['data']['image'];
    check(currentImage($targetId) === $path1, 'Không lưu đường dẫn ảnh.');
    check((bool) preg_match('~^uploads/[a-f0-9]{32}\.png$~D', $path1), 'Tên ảnh không đúng quy tắc duy nhất.');
    check(file_get_contents($base . '/' . $path1) === file_get_contents($png), 'Ảnh không được phục vụ đúng dữ liệu.');

    $updated = requestApi('PUT', 'products.php?id=' . $targetId, ['name' => 'Đã sửa không chọn ảnh', 'price' => 9000, 'stock' => 0]);
    check($updated['body']['success'] && currentImage($targetId) === $path1, 'Sửa JSON làm mất ảnh cũ.');
    $text = tempnam(sys_get_temp_dir(), 'vue9_');
    $temporaryFiles[] = $text;
    file_put_contents($text, 'Khong phai anh PNG');
    check(upload($targetId, $text)['status'] === 422, 'Tin MIME/đuôi giả thay vì kiểm tra ảnh thực tế.');
    check(currentImage($targetId) === $path1, 'Upload sai loại thay đổi ảnh cũ.');
    $large = tempnam(sys_get_temp_dir(), 'vue9_');
    $temporaryFiles[] = $large;
    file_put_contents($large, file_get_contents($png) . str_repeat('x', 2 * 1024 * 1024));
    check(upload($targetId, $large)['status'] === 413, 'Ảnh vượt 2 MB không bị từ chối.');
    check(currentImage($targetId) === $path1, 'Upload quá lớn thay đổi ảnh cũ.');
    check(upload(2147483647, $png)['status'] === 404, 'Cho upload vào sản phẩm không tồn tại.');
    check(requestApi('GET', 'upload.php')['status'] === 405, 'Upload cho phép phương thức sai.');
    check(requestApi('POST', 'upload.php', ['product_id' => $targetId])['status'] === 400, 'Cho upload thiếu file.');
    $second = upload($targetId, $png);
    $path2 = $second['body']['data']['image'];
    check($second['body']['success'] && $path2 !== $path1, 'Tên file upload bị trùng.');
    check(currentImage($targetId) === $path2, 'Upload lại chưa thay ảnh.');
    check(!file_exists(__DIR__ . '/../' . $path1), 'Ảnh cũ chưa được dọn sau upload thành công.');
    check(requestApi('POST', 'products.php', ['name' => 'Sai', 'price' => 5000, 'stock' => -1])['status'] === 422, 'Tồn kho âm không bị từ chối.');
    check(requestApi('GET', 'products.php')['body']['pagination']['total'] === 12, 'Yêu cầu sai vẫn thêm dữ liệu.');

    // Hai sản phẩm cũ nhất nằm ở trang 3; xóa lần lượt để trang cuối chỉ còn một dòng rồi biến mất.
    foreach ([$ids[0], $ids[1]] as $id) {
        check(requestApi('DELETE', 'products.php?id=' . $id)['body']['success'], 'Xóa sản phẩm thất bại.');
        $ids = array_values(array_diff($ids, [$id]));
    }
    $last = requestApi('GET', 'products.php?page=3')['body'];
    check($last['pagination']['page'] === 2 && $last['pagination']['total'] === 10 && count($last['data']) === 5, 'Không tự về trang hợp lệ sau khi xóa dòng cuối.');
    foreach ($ids as $id) requestApi('DELETE', 'products.php?id=' . $id);
    $ids = [];
    $empty = requestApi('GET', 'products.php?page=99')['body'];
    check($empty['data'] === [] && $empty['pagination']['total'] === 0 && $empty['pagination']['page'] === 1 && $empty['pagination']['total_pages'] === 1, 'Sai trạng thái khi xóa hết.');
    check(!file_exists(__DIR__ . '/../' . $path2), 'Xóa sản phẩm còn để lại ảnh.');
    echo "PASS: $checks kiểm tra API upload và phân trang.\n";
} catch (Throwable $exception) {
    $failed = true;
    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . "\n");
} finally {
    foreach ($ids as $id) {
        try { requestApi('DELETE', 'products.php?id=' . $id); }
        catch (Throwable $exception) { fwrite(STDERR, "Chưa dọn được sản phẩm kiểm tra #$id.\n"); }
    }
    foreach ($temporaryFiles as $file) if (is_file($file)) unlink($file);
}
exit($failed ? 1 : 0);
