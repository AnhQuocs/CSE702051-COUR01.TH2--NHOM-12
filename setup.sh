#!/bin/bash

echo "🚀 Setting up Laravel Project..."

# 1. Copy environment file
if [ ! -f .env ]; then
    echo "📋 Copying .env.example to .env..."
    cp .env.example .env
else
    echo "✅ .env file already exists"
fi

# 2. Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install

# 3. Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install

# 4. Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# 5. Create database file (for SQLite)
if [ ! -f database/database.sqlite ]; then
    echo "🗄️ Creating SQLite database..."
    touch database/database.sqlite
else
    echo "✅ Database file already exists"
fi

# 6. Run migrations
echo "🔄 Running database migrations..."
php artisan migrate

# 7. Run seeders (optional)
echo "🌱 Running database seeders..."
php artisan db:seed --force

# 8. Build assets
echo "🎨 Building assets..."
npm run build

# 9. Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 10. Set permissions (Linux/Mac only)
if [[ "$OSTYPE" == "linux-gnu"* ]] || [[ "$OSTYPE" == "darwin"* ]]; then
    echo "🔒 Setting permissions..."
    chmod -R 775 storage bootstrap/cache
fi

echo "✅ Setup completed successfully!"
echo "🌐 You can now run: php artisan serve"
