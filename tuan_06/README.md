# Bài tập thực hành tuần 6 - VueJS

## Chạy bài

- Mở `index.html` trực tiếp trong trình duyệt, hoặc truy cập thư mục `tuan_06/` qua web server đang phục vụ dự án.
- Từ trang chủ PHP của dự án, chọn **Tuần 6** (danh sách tuần được tạo tự động).
- Không cần npm, cơ sở dữ liệu hay Internet: đã kèm Vue **3.5.22** trong `assets/vendor/` cùng giấy phép MIT.

## Các bài

| Trang | Nội dung | Kiến thức |
| --- | --- | --- |
| `bai_1.html` | Thẻ sinh viên và khu vực chỉnh sửa dữ liệu | `createApp`, `data()`, `{{ }}`, `v-text`, `v-bind`, `v-if/else-if/else`, `:class`, `v-show` |
| `bai_2.html` | Đặt vé, tính tổng và xác nhận tại chỗ | `v-model.trim`, `v-model.number`, radio, checkbox, `methods`, `computed`, `@submit.prevent` |
| `bai_3.html` | Thêm, đánh dấu, xóa và lọc công việc | `v-for`, `:key`, checkbox, `computed`, `@submit.prevent`, `@click.stop` |

Mỗi bài có một file JavaScript riêng trong `assets/`, dùng Options API để dễ đối chiếu với đề. Dữ liệu chỉ tồn tại trong phiên trang hiện tại và được đặt lại khi tải lại trang.

## Kiểm tra theo đề

1. **Sinh viên:** nhập lần lượt điểm `8`, `6.5`, `5`, `4.9` để nhận Giỏi, Khá, Trung bình, Chưa đạt và màu tương ứng. Điểm 10 vẫn là Giỏi; điểm rỗng, âm hoặc trên 10 báo không hợp lệ. Bỏ chọn trạng thái để hiện “Đã nghỉ”; nút giới thiệu sử dụng `v-show`.
2. **Đặt vé:** nhập tên, chọn VIP, nhập 2 vé và chọn combo: tổng tiền **245.000 đ**. Chỉ tính combo một lần mỗi đơn. Tên rỗng hoặc chỉ có khoảng trắng, số lượng rỗng, lẻ, dưới 1 hoặc trên 10 đều không đặt được. Nút tăng/giảm dừng ở 1 và 10. Xác nhận giữ thông tin của đơn đã gửi; đặt lại form sẽ xóa xác nhận.
3. **Công việc:** thêm bằng nút hoặc Enter; thử tên rỗng; đổi trạng thái, kiểm tra ba bộ lọc và các số đếm; xóa từng việc và xóa tất cả việc hoàn thành. Khi hết việc hoặc bộ lọc không có kết quả, trang hiển thị thông báo tương ứng.

Tài liệu tham khảo: [Vue - Quick Start](https://vuejs.org/guide/quick-start.html), [Form Input Bindings](https://vuejs.org/guide/essentials/forms.html).
