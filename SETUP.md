# 🚀 Hướng dẫn cài đặt dự án Laravel

## Cài đặt nhanh (Khuyến nghị)

### Cho Windows:
```bash
.\setup.bat
```

### Cho Linux/Mac:
```bash
chmod +x setup.sh
./setup.sh
```

## Các bước cài đặt thủ công

Nếu cài đặt tự động không hoạt động, hãy làm theo các bước sau:

### 1. Cấu hình môi trường
```bash
# Sao chép file môi trường
cp .env.example .env  # Linux/Mac
copy .env.example .env  # Windows
```

### 2. Cài đặt dependencies
```bash
# Cài đặt dependencies PHP
composer install

# Cài đặt dependencies Node.js
npm install
```

### 3. Thiết lập ứng dụng
```bash
# Tạo application key
php artisan key:generate
```

### 4. Thiết lập database
```bash
# Tạo database SQLite (nếu sử dụng SQLite)
touch database/database.sqlite  # Linux/Mac
type nul > database\database.sqlite  # Windows

# Chạy migrations
php artisan migrate

# Chạy seeders (tùy chọn)
php artisan db:seed
```

### 5. Build assets
```bash
# Cho production
npm run build

# Cho development (với file watching)
npm run dev
```

### 6. Xóa cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 7. Thiết lập quyền (Chỉ Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
```

## Yêu cầu hệ thống

- **PHP >= 8.2** với các extensions:
  - mbstring
  - xml
  - ctype
  - json
  - bcmath
  - fileinfo
  - tokenizer
  - sqlite3 (nếu sử dụng SQLite)

- **Composer** (PHP dependency manager)
- **Node.js >= 16** và npm
- **Git**

## Các vấn đề thường gặp & Giải pháp

### Vấn đề: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Vấn đề: Lỗi "Class not found"
```bash
composer dump-autoload
```

### Vấn đề: Lỗi kết nối database
- Kiểm tra cấu hình database trong file `.env`
- Đảm bảo database đã tồn tại
- Chạy `php artisan migrate`

### Vấn đề: Permission denied (Linux/Mac)
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Vấn đề: Vite assets không load
```bash
npm run build
```

## Chạy ứng dụng

```bash
# Khởi động development server
php artisan serve

# Truy cập tại: http://localhost:8000
```

## Biến môi trường

Các biến chính cần cấu hình trong `.env`:

```env
APP_NAME="Hệ thống quản lý dự án"
APP_ENV=local
APP_KEY=base64:... # Tự động tạo
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Hoặc cho MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

MAIL_MAILER=log
# Cấu hình để gửi email thật nếu cần
```
