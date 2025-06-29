# Hướng dẫn khởi động nhanh

## Cho người dùng mới clone dự án này

### Windows (PowerShell/CMD)
```batch
git clone <repository-url>
cd CSE702051-COUR01.TH2--NHOM-12
./setup-auto.bat
```

### Linux/macOS/WSL
```bash
git clone <repository-url>
cd CSE702051-COUR01.TH2--NHOM-12
chmod +x setup-auto.sh
./setup-auto.sh
```

### Phương án thay thế (nếu cài đặt tự động thất bại)
```bash
./setup.bat    # Windows
./setup.sh     # Linux/macOS
```

## Những gì được cài đặt tự động

Kiểm tra PHP 8.2+ và các extensions cần thiết  
Xác minh cài đặt Composer  
Cài đặt Laravel dependencies  
Tạo file .env từ template  
Tạo application key  
Tạo SQLite database  
Chạy database migrations  
Build frontend assets (nếu có Node.js)  
Tối ưu ứng dụng  

## Sau khi cài đặt

```bash
# Khởi động server
php artisan serve

# Truy cập: http://localhost:8000
# Đăng ký tài khoản và bắt đầu sử dụng!
```

## Nếu gặp lỗi

Script cài đặt tự động cung cấp thông báo lỗi chi tiết với links download và giải pháp cho bất kỳ yêu cầu nào bị thiếu.

Các cách khắc phục thường gặp:
- Cài đặt PHP 8.2+ từ php.net
- Cài đặt Composer từ getcomposer.org  
- Bật các PHP extensions cần thiết
- Chạy với quyền Administrator/sudo nếu có vấn đề về quyền
