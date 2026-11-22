<?php

require_once __DIR__ . '/../common/api.php';
$pdo = null;
$newPath = null;

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        respond(['success' => false, 'message' => 'Upload ảnh phải dùng POST multipart/form-data.'], 405);
    }
    // PHP có thể bỏ toàn bộ POST/FILES khi request vượt post_max_size.
    if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 2 * 1024 * 1024) {
        respond(['success' => false, 'message' => 'Ảnh vượt dung lượng tối đa 2 MB.'], 413);
    }
    $id = positiveInteger($_POST['product_id'] ?? null, 'Mã sản phẩm');
    $file = $_FILES['image'] ?? null;
    if (!$file || !isset($file['error']) || is_array($file['error'])) {
        respond(['success' => false, 'message' => 'Vui lòng gửi một file ảnh trong trường image.'], 400);
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        respond(['success' => false, 'message' => 'Ảnh vượt dung lượng cho phép, tối đa 2 MB.'], 413);
    }
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        respond(['success' => false, 'message' => 'Chưa nhận được ảnh đầy đủ. Vui lòng chọn ảnh và thử lại.'], 400);
    }
    $size = filesize($file['tmp_name']);
    if ($size === false || $size === 0 || $size > 2 * 1024 * 1024) {
        respond(['success' => false, 'message' => 'Ảnh phải có dữ liệu và dung lượng không quá 2 MB.'], 413);
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    $imageInfo = @getimagesize($file['tmp_name']);
    $originalExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!isset($extensions[$mime]) || !$imageInfo || $imageInfo['mime'] !== $mime
        || !in_array($originalExtension, ['jpg', 'jpeg', 'png'], true)) {
        respond(['success' => false, 'message' => 'Chỉ chấp nhận ảnh JPG, JPEG hoặc PNG hợp lệ.'], 422);
    }

    $pdo = dbConnect();
    $pdo->beginTransaction();
    $statement = $pdo->prepare('SELECT image FROM products WHERE id = ? FOR UPDATE');
    $statement->execute([$id]);
    $old = $statement->fetch();
    if (!$old) {
        $pdo->rollBack();
        respond(['success' => false, 'message' => 'Không tìm thấy sản phẩm để gắn ảnh.'], 404);
    }

    $directory = __DIR__ . '/../uploads';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Không tạo được thư mục uploads.');
    }
    $newPath = 'uploads/' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $newPath)) {
        throw new RuntimeException('Không lưu được file ảnh.');
    }
    $statement = $pdo->prepare('UPDATE products SET image = ? WHERE id = ?');
    $statement->execute([$newPath, $id]);
    $pdo->commit();

    // Chỉ bỏ ảnh cũ sau khi file mới và đường dẫn trong database đều đã lưu.
    deleteImage($old['image']);
    respond(['success' => true, 'message' => 'Đã upload ảnh.', 'data' => ['id' => $id, 'image' => $newPath]]);
} catch (Throwable $exception) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    deleteImage($newPath);
    error_log($exception->getMessage());
    respond(['success' => false, 'message' => 'Không upload được ảnh. Thông tin sản phẩm và ảnh cũ vẫn được giữ, bạn có thể thử lại.'], 500);
}
