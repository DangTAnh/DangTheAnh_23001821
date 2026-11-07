# Bài tập tuần 7 - VueJS

Mở `index.html` bằng trình duyệt hoặc chọn Tuần 7 từ trang chủ PHP. Thư viện Vue 3.5.22 được lưu trong `assets/vendor/`, không cần npm hay Internet.

- `bai_1.html`: 6 sản phẩm thuộc 3 danh mục; tìm kiếm, lọc, sắp xếp, thêm/xóa và thống kê. Có `watch` từ khóa và `mounted()`.
- `bai_2.html`: component `the-san-pham` trong `components/TheSanPham.js`, nhận props có kiểu dữ liệu, required/default; mỗi thẻ tự quản lý trạng thái mô tả.
- `bai_3.html`: component `dong-gio-hang` chỉ phát sự kiện `doi-so-luong` và `yeu-cau-xoa`; component cha kiểm tra dữ liệu, cập nhật giỏ và tính tổng.

Dữ liệu được đặt lại khi tải lại trang. Nút Mua ở bài 2 hiển thị thông báo lựa chọn để minh họa tương tác.

Kiểm tra bài 3: 2 bút x 5.000 đ + 3 vở x 12.000 đ = 5 sản phẩm, tổng 46.000 đ.

Tài liệu: [Props](https://vuejs.org/guide/components/props.html), [Component Events](https://vuejs.org/guide/components/events.html), [Watchers](https://vuejs.org/guide/essentials/watchers.html).
