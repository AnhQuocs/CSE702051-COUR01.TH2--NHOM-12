# 🚀 Laravel Project Setup Guide

## Quick Setup (Recommended)

### For Windows:
```bash
.\setup.bat
```

### For Linux/Mac:
```bash
chmod +x setup.sh
./setup.sh
```

## Manual Setup Steps

If the automatic setup doesn't work, follow these steps manually:

### 1. Environment Configuration
```bash
# Copy environment file
cp .env.example .env  # Linux/Mac
copy .env.example .env  # Windows
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Application Setup
```bash
# Generate application key
php artisan key:generate
```

### 4. Database Setup
```bash
# Create SQLite database (if using SQLite)
touch database/database.sqlite  # Linux/Mac
type nul > database\database.sqlite  # Windows

# Run migrations
php artisan migrate

# Run seeders (optional)
php artisan db:seed
```

### 5. Build Assets
```bash
# For production
npm run build

# For development (with file watching)
npm run dev
```

### 6. Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 7. Set Permissions (Linux/Mac only)
```bash
chmod -R 775 storage bootstrap/cache
```

## Requirements

- **PHP >= 8.2** with extensions:
  - mbstring
  - xml
  - ctype
  - json
  - bcmath
  - fileinfo
  - tokenizer
  - sqlite3 (if using SQLite)

- **Composer** (PHP dependency manager)
- **Node.js >= 16** and npm
- **Git**

## Common Issues & Solutions

### Issue: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Issue: "Class not found" errors
```bash
composer dump-autoload
```

### Issue: Database connection error
- Check `.env` file database configuration
- Ensure database exists
- Run `php artisan migrate`

### Issue: Permission denied (Linux/Mac)
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Issue: Vite assets not loading
```bash
npm run build
```

## Running the Application

```bash
# Start development server
php artisan serve

# Access at: http://localhost:8000
```

## Environment Variables

Key variables to configure in `.env`:

```env
APP_NAME="Project Management System"
APP_ENV=local
APP_KEY=base64:... # Generated automatically
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

MAIL_MAILER=log
# Configure for real email sending if needed
```
