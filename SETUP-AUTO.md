# 🚀 Hướng dẫn cài đặt tự động Laravel

## Khởi động nhanh (Cài đặt một cú click)

### Người dùng Windows
```bash
# Clone dự án
git clone <repository-url>
cd CSE702051-COUR01.TH2--NHOM-12

# Chạy script cài đặt tự động nâng cao
./setup-auto.bat
```

### Người dùng Linux/macOS/WSL  
```bash
# Clone dự án
git clone <repository-url>
cd CSE702051-COUR01.TH2--NHOM-12

# Cấp quyền thực thi và chạy
chmod +x setup-auto.sh
./setup-auto.sh
```

## Script tự động thực hiện

### ✅ Kiểm tra hệ thống
- Xác minh cài đặt PHP 8.2+
- Kiểm tra tất cả PHP extensions cần thiết
- Xác thực cài đặt Composer
- Phát hiện Node.js (tùy chọn)

### ✅ Cài đặt thông minh
- Cài đặt PHP dependencies tối ưu cho production
- Sao chép `.env.example` thành `.env` nếu cần
- Tạo application key duy nhất
- Tạo database SQLite tự động
- Chạy database migrations
- Build frontend assets (nếu có Node.js)
- Tối ưu application caches

### ✅ Xử lý lỗi
- Thông báo lỗi rõ ràng với giải pháp
- Gợi ý troubleshooting tự động
- Links download các phần mềm cần thiết
- Hướng dẫn khôi phục từng bước

## Phương án dự phòng: Cài đặt thủ công

Nếu cài đặt tự động thất bại, sử dụng setup gốc:

**Windows:**
```bash
./setup.bat
```

**Linux/macOS:**
```bash
chmod +x setup.sh
./setup.sh
```

## Kiểm tra yêu cầu hệ thống tự động

Script tự động xác minh:

### Phần mềm cần thiết
| Thành phần | Phiên bản tối thiểu | Tự động phát hiện |
|-----------|----------------|---------------|
| PHP | 8.2+ | ✅ Có |
| Composer | Mới nhất | ✅ Có |
| Node.js | 18+ | ✅ Tùy chọn |

### PHP Extensions cần thiết
- ✅ PDO & PDO SQLite
- ✅ OpenSSL & MBString
- ✅ Tokenizer & XML
- ✅ CType & JSON
- ✅ BCMath & Fileinfo

## Sau khi cài đặt

Sau khi cài đặt thành công:

### 1. Khởi động Development Server
```bash
php artisan serve
```

### 2. Truy cập ứng dụng
Truy cập: **http://localhost:8000**

### 3. Tạo tài khoản
1. Vào `/register`
2. Tạo tài khoản admin
3. Bắt đầu quản lý dự án!

## Tính năng dự án sẵn sàng sử dụng

### 🎯 Chức năng cốt lõi
- ✅ Hệ thống xác thực người dùng
- ✅ Thao tác CRUD dự án
- ✅ Quản lý công việc con
- ✅ Theo dõi tiến độ
- ✅ Danh mục & Nhãn
- ✅ Phân tích Dashboard

### 🔧 Tính năng kỹ thuật
- ✅ RESTful API Endpoints
- ✅ Form Validation
- ✅ CSRF Protection
- ✅ Route Model Binding
- ✅ Responsive UI Design
- ✅ SQLite Database

## Khắc phục sự cố cài đặt tự động

### Nếu cài đặt thất bại

**Kiểm tra thông báo lỗi chi tiết** - script cung cấp giải pháp cụ thể cho từng vấn đề.

### Giải pháp thường gặp

1. **Thiếu PHP**: Cài đặt từ https://www.php.net/downloads
2. **Thiếu Composer**: Cài đặt từ https://getcomposer.org/download/
3. **Thiếu Extensions**: Bật trong file `php.ini`
4. **Lỗi quyền**: Chạy với quyền Administrator (Windows) hoặc `sudo` (Linux/macOS)

### Khôi phục thủ công
```bash
# Nếu cài đặt tự động thất bại một phần, bạn có thể tiếp tục thủ công:
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
```

## Lệnh phát triển

### Lệnh hữu ích sau khi cài đặt
```bash
# Reset database
php artisan migrate:fresh

# Laravel shell tương tác
php artisan tinker

# Xem tất cả routes
php artisan route:list

# Xóa tất cả caches
php artisan optimize:clear

# Build assets cho development
npm run dev

# Build assets cho production
npm run build
```

## Tổng quan kiến trúc

### Cấu trúc file
```
├── app/Http/Controllers/    # Business Logic
├── app/Models/             # Database Models
├── database/migrations/    # Database Schema
├── resources/views/        # Blade Templates
├── routes/web.php         # Web Routes
├── routes/api.php         # API Routes
├── .env                   # Environment Config
└── database/database.sqlite # SQLite Database
```

### Công nghệ chính
- **Framework**: Laravel 12
- **Database**: SQLite (không cần cấu hình)
- **Frontend**: Blade + Tailwind CSS
- **Authentication**: Laravel Breeze
- **API**: Laravel Sanctum

## Hỗ trợ

### Nhận trợ giúp
1. Kiểm tra error logs: `storage/logs/laravel.log`
2. Bật debug mode: `APP_DEBUG=true` trong `.env`
3. Chạy lại setup script sau khi sửa lỗi

### Yêu cầu hệ thống
- **OS**: Windows 10+, macOS 10.15+, Ubuntu 18.04+
- **RAM**: Tối thiểu 512MB, khuyến nghị 1GB
- **Ổ cứng**: 100MB dung lượng trống
- **Mạng**: Internet để tải dependencies lần đầu

---

**🎓 Dự án giáo dục**  
CSE702051-COUR01.TH2--NHOM-12  
Hệ thống quản lý dự án Laravel
