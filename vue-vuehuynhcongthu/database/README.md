# Cơ sở dữ liệu XAMPP

File `vuon_uom_xanh.sql` tạo cơ sở dữ liệu MySQL `vuon_uom_xanh` cho trang Vườn Ươm Xanh.

1. Mở **XAMPP Control Panel**, bật **Apache** và **MySQL**.
2. Truy cập `http://localhost/phpmyadmin`.
3. Chọn **Import**, chọn file `database/vuon_uom_xanh.sql`, rồi nhấn **Import/Go**.
4. Xác nhận database `vuon_uom_xanh` xuất hiện ở cột trái.

Script bao gồm các bảng: tài khoản, danh mục/cây giống, khu/lô ươm, lịch chăm sóc, đơn hàng/chi tiết đơn, minh chứng thanh toán và lịch sử giá.

Lưu ý: Vue hiện đang dùng `localStorage`, nên import SQL chỉ tạo dữ liệu MySQL, chưa tự kết nối giao diện. Để dùng CSDL, cần thêm backend (PHP/Node.js) và API cho đăng ký, đăng nhập, sản phẩm, đơn hàng.
