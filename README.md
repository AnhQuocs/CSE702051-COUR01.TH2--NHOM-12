# HỆ THỐNG QUẢN LÝ DỰ ÁN CÁ NHÂN

> **My Project Hub** - Hệ thống quản lý dự án hiện đại với Laravel và Tailwind CSS

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## Tổng quan

Hệ thống quản lý dự án cá nhân được xây dựng với Laravel, cung cấp giao diện web hiện đại và API RESTful có:

### Tính năng chính
- **Xác thực người dùng** (đăng ký, đăng nhập, đăng xuất)
- **Quản lý dự án** (CRUD, lọc, tìm kiếm, phân trang)
- **Hệ thống nhãn và danh mục** cho dự án
- **Quản lý công việc con** (subtasks) với checkbox toggle
- **Thống kê và báo cáo** chi tiết (xuất CSV/JSON)
- **Quản lý profile người dùng** (chỉnh sửa thông tin, đổi mật khẩu)
- **Nhắc nhở qua email** tự động (command scheduler)
- **API RESTful** với Laravel Sanctum
- **Giao diện responsive** với Tailwind CSS
- **Giao diện tiếng Việt** hoàn chỉnh
- **Bảo mật cao** với authorization và validation

---

## Mô hình dữ liệu

### Users
| Trường | Kiểu | Mô tả |
|--------|------|-------|
| id | UUID | ID duy nhất |
| name | String | Tên hiển thị |
| email | String | Email đăng nhập |
| password | String | Mật khẩu mã hóa |

### Projects  
| Trường | Kiểu | Mô tả |
|--------|------|-------|
| id | UUID | ID duy nhất |
| user_id | UUID | Chủ sở hữu dự án |
| category_id | UUID | Danh mục (nullable) |
| title | String | Tiêu đề dự án |
| description | Text | Mô tả chi tiết |
| priority | Enum | low, medium, high |
| status | Enum | not_started, in_progress, completed, on_hold |
| start_date | Date | Ngày bắt đầu |
| end_date | Date | Ngày kết thúc |
| reminder_time | DateTime | Thời gian nhắc nhở |

### Tags & Categories
- **Categories**: Phân loại dự án theo chủ đề
- **Tags**: Gắn nhãn linh hoạt cho dự án  
- **Project_Tag**: Bảng pivot liên kết dự án và nhãn
- **Subtasks**: Công việc con với trạng thái hoàn thành

---

## API Endpoints

### Authentication
```http
POST   /api/login               # Đăng nhập và lấy token
POST   /api/logout              # Đăng xuất (xóa token)
GET    /api/user                # Thông tin user hiện tại
```

### Projects Management
```http
GET    /api/projects            # Lấy danh sách dự án
POST   /api/projects            # Tạo dự án mới  
GET    /api/projects/{id}       # Chi tiết dự án
PUT    /api/projects/{id}       # Cập nhật dự án
DELETE /api/projects/{id}       # Xóa dự án
```

### Statistics
```http
GET /api/project-stats          # Thống kê dự án qua API
```

### Web Interface
```http
GET /                            # Trang chủ
GET /dashboard                   # Dashboard chính  
GET /projects                    # Danh sách dự án
GET /projects/create             # Tạo dự án mới
GET /projects/{id}               # Chi tiết dự án
GET /projects/{id}/edit          # Chỉnh sửa dự án
GET /profile                     # Chỉnh sửa profile
PATCH /profile                   # Cập nhật profile
DELETE /profile                  # Xóa tài khoản
GET /stats                       # Trang thống kê chi tiết
GET /stats/export/{format}       # Xuất báo cáo (CSV/JSON)
GET /stats/report                # Báo cáo nâng cao
```

### Subtasks Management  
```http
POST   /projects/{id}/subtasks           # Tạo công việc con
PATCH  /subtasks/{id}/toggle             # Chuyển đổi trạng thái
DELETE /subtasks/{id}                    # Xóa công việc con
```

---

## Công nghệ sử dụng

### Backend
- **Laravel 12.x** - PHP Framework
- **MySQL** - Database  
- **Laravel Sanctum** - API Authentication
- **Laravel Breeze** - Authentication UI
- **Carbon** - Date manipulation

### Frontend  
- **Blade Templates** - Server-side rendering
- **Tailwind CSS** - Utility-first CSS
- **Alpine.js** - Lightweight JavaScript
- **JavaScript** - Interactive UI components

### Development Tools
- **Composer** - PHP dependency manager
- **NPM/Vite** - Asset bundling
- **Git** - Version control
- **Custom Middleware** - Request logging

---

## Cài đặt và chạy dự án

### Yêu cầu hệ thống
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 8.0 (hoặc SQLite)

### Cài đặt tự động (Khuyến nghị)

**Bước 1: Clone repository**
```bash
git clone https://github.com/AnhQuocs/CSE702051-COUR01.TH2--NHOM-12
cd CSE702051-COUR01.TH2--NHOM-12
```

**Bước 2: Chạy script tự động**

**Cho Windows (PowerShell/CMD):**
```bash
.\setup.bat
```

**Cho Linux/Mac (Terminal):**
```bash
chmod +x setup.sh
./setup.sh
```

>  Script này sẽ tự động:
* Cài các dependencies PHP và Node
* Tạo file .env và khóa mã hóa
* Tạo database (SQLite) và chạy migrations
* Build frontend với Vite
Xem chi tiết file SETUP.md nếu gặp lỗi trong quá trình cài đặt.

### Hướng dẫn cài đặt thủ công

1. **Clone repository**
```bash
git clone https://github.com/AnhQuocs/CSE702051-COUR01.TH2--NHOM-12
cd CSE702051-COUR01.TH2--NHOM-12
```

2. **Cài đặt dependencies**
```bash
composer install
npm install
```

3. **Cấu hình môi trường**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Cấu hình database trong `.env`**

**Cho SQLite (mặc định - đơn giản):**
```env
DB_CONNECTION=sqlite
```
Tạo file database nếu chưa có:
```bash
type nul > database/database.sqlite  # (Windows)
touch database/database.sqlite       # (Linux/macOS)
```

**Hoặc cho MySQL (XAMPP):**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=root  
DB_PASSWORD=
```

5. **Chạy migration và seeder**
```bash
php artisan migrate
php artisan db:seed
```

6. **Build assets**
```bash
npm run dev
# Hoặc cho production: npm run build
```

7. **Khởi động server**
```bash
php artisan serve
```

8. **Truy cập ứng dụng**
- Web: http://localhost:8000
- API: http://localhost:8000/api

---

## Artisan Commands

### Email Reminders
```bash
# Gửi email nhắc nhở cho các dự án sắp hết hạn
php artisan projects:send-reminders
```

### Database Management
```bash
# Chạy migrations
php artisan migrate

# Rollback migrations  
php artisan migrate:rollback

# Seed database
php artisan db:seed

# Refresh database
php artisan migrate:refresh --seed
```

### Cache & Optimization
```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Optimize for production
php artisan optimize
```

---

## Bảo mật

- **Authentication**: Laravel Sanctum
- **Authorization**: Ownership-based access control  
- **Validation**: Form Request validation
- **CSRF Protection**: Built-in Laravel protection
- **SQL Injection**: Eloquent ORM protection
- **XSS Protection**: Blade template escaping

---

## Cấu trúc thư mục

```
project/
├── app/
│   ├── Http/Controllers/     # Controllers
│   ├── Models/              # Eloquent Models  
│   ├── Requests/            # Form Requests
│   └── Mail/                # Email templates
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Data seeders
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes  
└── public/                  # Public assets
```

---

## Đóng góp

1. Fork repository
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)  
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

---

## Giấy phép

Dự án này được phân phối dưới giấy phép MIT. Xem file [LICENSE](LICENSE) để biết thêm chi tiết.

---

## Liên hệ

- **Dự án**: CSE702051-COUR01.TH2--NHOM-12
- **Môn học**: Web Nâng Cao (PHP Laravel)  
- **Nhóm phát triển**: Nhóm 12
- **GitHub**: https://github.com/AnhQuocs/CSE702051-COUR01.TH2--NHOM-12

---
