# PHP API cho XAMPP

## Cài đặt

1. Import `database/vuon_uom_xanh.sql` bằng phpMyAdmin.
2. Chép toàn bộ dự án vào `C:\xampp\htdocs\vuon-uom-xanh` (hoặc tạo Virtual Host trỏ tới thư mục dự án).
3. Bật Apache và MySQL trong XAMPP. Mở `http://localhost/vuon-uom-xanh/backend/api/?route=plants` để kiểm tra API.
4. Nếu MySQL không dùng `root` và mật khẩu rỗng, sửa `backend/api/config.php`.

## API hiện có

- `POST ?route=auth/register`: `fullname`, `email`, `password`.
- `POST ?route=auth/login`: `email`, `password`.
- `GET ?route=plants`.
- `GET ?route=lots` và `PATCH ?route=lots/{id}/status`.
- `POST ?route=orders`: thông tin khách hàng và `items: [{ plant_id, quantity }]`.

Khi chạy Vue bằng Vite, tạo `.env.local` với:

```env
VITE_API_BASE_URL=http://localhost/vuon-uom-xanh/backend/api/index.php
```

Không đưa `config.php` lên Git nếu bạn đặt mật khẩu CSDL thật; hãy dùng biến môi trường trên hosting thực tế.
