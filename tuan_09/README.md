# Bài tập tuần 9 - Upload ảnh và phân trang

Bản mở rộng bài CRUD tuần 8, đặt trong thư mục riêng để chạy độc lập. Database mặc định là `web_tuan_09`.

## Chạy bài

1. Bật MySQL trong XAMPP, nhập `database.sql` bằng phpMyAdmin. Database có 12 sản phẩm mẫu; cột `image VARCHAR(255) NULL` lưu đường dẫn ảnh.
2. Bật máy chủ PHP cho thư mục dự án:

```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8060 -t D:\web
```

3. Mở <http://127.0.0.1:8060/tuan_09/>. Không mở trực tiếp bằng `file://`.

Cần PHP 7.4+ với `pdo_mysql`, `mbstring`, `fileinfo`; MySQL/MariaDB và quyền ghi thư mục `uploads`. Kết nối mặc định theo XAMPP: `127.0.0.1:3306`, user `root`, mật khẩu trống. Có thể đặt `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` trước khi chạy PHP. Vue đã kèm trong `assets/vendor/`.

`database.sql` không xóa bảng và chỉ thêm dữ liệu mẫu khi bảng rỗng. Nếu dùng bảng cũ của tuần 8 thay cho database riêng, thêm cột trước khi chạy:

```sql
ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL;
```

## Chức năng

- Thêm/sửa thông tin bằng JSON qua `api/products.php`, giữ lại ảnh nếu không chọn ảnh mới.
- Sau khi lưu thông tin, gửi FormData gồm `product_id` và `image` đến `POST api/upload.php`.
- Vue kiểm tra đuôi file, MIME và dung lượng; PHP kiểm tra lại dung lượng thực tế, MIME bằng Fileinfo và nội dung ảnh bằng `getimagesize`.
- Chỉ nhận JPG/JPEG/PNG tối đa 2 MB (2.097.152 byte), lưu file với tên ngẫu nhiên. Chỉ thay ảnh cũ sau khi file và database cập nhật thành công.
- Nếu upload thất bại, giữ thông tin đã lưu, ảnh cũ, file vừa chọn và mã sản phẩm để nút **Thử lại upload** chỉ gửi ảnh, không tạo lại sản phẩm.
- `GET api/products.php?page=1&limit=5` trả `success`, `data`, `pagination` gồm `page`, `limit`, `total`, `total_pages`. Thiếu tham số dùng 1/5; tham số sai báo lỗi. Backend dùng `COUNT`, `ORDER BY id DESC`, `LIMIT`, `OFFSET`.
- Thêm xong về trang 1; sửa/xóa tải lại trang hiện tại và lấy page thực tế từ API. Trang rỗng là 1/1, tổng 0, khóa Trước/Sau.
- Khi thay ảnh hoặc xóa sản phẩm, ứng dụng dọn file ảnh cũ. Các ảnh upload không đưa vào Git.

## Kiểm tra

Kiểm tra luồng Vue bằng Node.js 18+ (không cần npm):

```powershell
node D:\web\tuan_09\tests\app_test.cjs
```

Kiểm tra API cần **database kiểm tra riêng và rỗng**, không chạy trên database bài tập. Tạo cấu trúc trong phpMyAdmin:

```sql
CREATE DATABASE web_tuan_09_test CHARACTER SET utf8mb4;
CREATE TABLE web_tuan_09_test.products LIKE web_tuan_09.products;
```

Mở một PowerShell riêng, chạy PHP trỏ vào database kiểm tra:

```powershell
$env:DB_NAME = 'web_tuan_09_test'
C:\xampp\php\php.exe -S 127.0.0.1:8061 -t D:\web
```

Từ PowerShell khác:

```powershell
C:\xampp\php\php.exe D:\web\tuan_09\tests\api_test.php
```

Bài kiểm tra tạo/xóa dữ liệu riêng, kiểm tra 12 dòng chia 5/5/2, tham số sai, vượt trang cuối, MIME giả, ảnh quá 2 MB, thay ảnh, giữ ảnh khi sửa và xóa hết trang cuối. Cần extension PHP `curl`. File và sản phẩm kiểm tra được dọn sau khi chạy.
