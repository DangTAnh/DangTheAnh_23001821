# Bài tập tuần 8 - PHP API và VueJS

## Chạy với XAMPP

1. Bật MySQL và Apache trong XAMPP.
2. Nhập file `database.sql` trong phpMyAdmin để tạo database `web_tuan_08`, bảng `products` và 3 sản phẩm mẫu. Script không xóa bảng hay dữ liệu đã có; chỉ thêm mẫu nếu bảng rỗng.
3. Mở thư mục `tuan_08/` qua máy chủ PHP đang phục vụ dự án. Trang chủ của dự án tự thêm liên kết Tuần 8.

Có thể dùng máy chủ PHP tích hợp từ thư mục gốc dự án:

```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8060 -t D:\web
```

Sau đó truy cập <http://127.0.0.1:8060/tuan_08/>. MySQL phải đang chạy. Không mở HTML bằng `file://` vì API cần máy chủ PHP.

Kết nối mặc định: host `127.0.0.1`, port `3306`, database `web_tuan_08`, user `root`, mật khẩu trống theo XAMPP cục bộ. Có thể sửa `common/dbConnect.php` hoặc đặt biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` trước khi khởi động PHP. Cần PHP 7.4+ với `pdo_mysql` và `mbstring`. Vue 3.5.22 được kèm trong `assets/vendor/`.

## API

| Phương thức | Đường dẫn | Chức năng |
| --- | --- | --- |
| GET | `api/products.php` | Trả `success` và mảng `data` |
| POST | `api/products.php` | Thêm sản phẩm từ JSON |
| PUT | `api/products.php?id=1` | Sửa sản phẩm từ JSON |
| DELETE | `api/products.php?id=1` | Xóa sản phẩm |

JSON khi thêm hoặc sửa:

```json
{"name":"Bút bi","price":5000,"stock":20}
```

PHP kiểm tra tên đã bỏ khoảng trắng dài 1-100 ký tự, giá là số nguyên dương, tồn kho là số nguyên không âm. Giá và tồn kho phải nằm trong giới hạn cột INT (tối đa 2.147.483.647); JSON phải gửi số, không gửi chuỗi số. Truy vấn có tham số dùng PDO prepared statement. Lỗi luôn có `success: false` và `message`.

Giao diện dùng `v-model`, gọi API khi mở trang và gọi lại GET sau mỗi lần ghi thành công. Nút Xóa hiển thị câu hỏi cùng hai nút Xác nhận xóa/Hủy ngay trên trang; chỉ gửi DELETE sau khi xác nhận.

## Kiểm tra

Khi MySQL và máy chủ PHP đang chạy:

```powershell
C:\xampp\php\php.exe D:\web\tuan_08\tests\api_test.php
```

Có thể truyền URL API khác làm đối số thứ nhất. Bài kiểm tra tạo sản phẩm riêng, kiểm tra lưu qua các yêu cầu GET mới, cập nhật giá, lưu lại dữ liệu không đổi, từ chối tồn kho -1 và dữ liệu sai, xử lý mã không tồn tại rồi xóa sản phẩm kiểm tra. Cần extension PHP `curl`.
