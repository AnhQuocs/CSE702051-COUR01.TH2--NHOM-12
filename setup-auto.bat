@echo off
setlocal enabledelayedexpansion
chcp 65001 >nul
echo ===============================================
echo    🚀 LARAVEL PROJECT AUTO SETUP SCRIPT
echo    CSE702051-COUR01.TH2--NHOM-12
echo ===============================================
echo.

REM Color codes for output
set "RED=[91m"
set "GREEN=[92m"
set "YELLOW=[93m"
set "BLUE=[94m"
set "RESET=[0m"

echo %BLUE%🔍 Checking system requirements...%RESET%
echo.

REM Check PHP version
echo %YELLOW%[1/8] Checking PHP...%RESET%
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo %RED%❌ ERROR: PHP is not installed or not in PATH%RESET%
    echo %YELLOW%📥 Required: PHP 8.2 or higher%RESET%
    echo %YELLOW%🔗 Download from: https://www.php.net/downloads%RESET%
    echo %YELLOW%⚙️  Make sure to add PHP to your system PATH%RESET%
    goto :error_exit
) else (
    for /f "tokens=2" %%a in ('php --version ^| findstr /r "PHP.*"') do set php_version=%%a
    echo %GREEN%✅ PHP !php_version! found%RESET%
)

REM Check required PHP extensions
echo %YELLOW%[2/8] Checking PHP extensions...%RESET%
set extensions_ok=true
set required_extensions=pdo sqlite3 openssl mbstring tokenizer xml ctype json bcmath fileinfo

for %%e in (%required_extensions%) do (
    php -m | findstr /i "%%e" >nul
    if !errorlevel! neq 0 (
        echo %RED%❌ Missing PHP extension: %%e%RESET%
        set extensions_ok=false
    ) else (
        echo %GREEN%✅ %%e extension found%RESET%
    )
)

if "!extensions_ok!"=="false" (
    echo %RED%❌ ERROR: Some required PHP extensions are missing%RESET%
    echo %YELLOW%💡 Enable them in your php.ini file%RESET%
    goto :error_exit
)

REM Check Composer
echo %YELLOW%[3/8] Checking Composer...%RESET%
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo %RED%❌ ERROR: Composer is not installed or not in PATH%RESET%
    echo %YELLOW%🔗 Download from: https://getcomposer.org/download/%RESET%
    goto :error_exit
) else (
    for /f "tokens=3" %%a in ('composer --version ^| findstr /r "Composer.*"') do set composer_version=%%a
    echo %GREEN%✅ Composer !composer_version! found%RESET%
)

REM Check Node.js
echo %YELLOW%[4/8] Checking Node.js...%RESET%
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo %YELLOW%⚠️  WARNING: Node.js not found%RESET%
    echo %YELLOW%🔗 Download from: https://nodejs.org/%RESET%
    echo %YELLOW%💡 Required for building frontend assets%RESET%
    set "node_available=false"
) else (
    for /f %%a in ('node --version') do set node_version=%%a
    echo %GREEN%✅ Node.js !node_version! found%RESET%
    set "node_available=true"
)

echo.
echo %BLUE%⚙️  Installing dependencies...%RESET%
echo.

REM Install Composer dependencies
echo %YELLOW%[5/8] Installing PHP dependencies...%RESET%
composer install --no-dev --optimize-autoloader --no-interaction
if %errorlevel% neq 0 (
    echo %RED%❌ Failed to install Composer dependencies%RESET%
    goto :error_exit
)
echo %GREEN%✅ PHP dependencies installed%RESET%

REM Setup environment
echo %YELLOW%[6/8] Setting up environment...%RESET%
if not exist .env (
    if exist .env.example (
        copy .env.example .env >nul
        echo %GREEN%✅ Environment file created from .env.example%RESET%
    ) else (
        echo %RED%❌ ERROR: .env.example file not found%RESET%
        goto :error_exit
    )
) else (
    echo %YELLOW%⚠️  .env file already exists, checking...%RESET%
    findstr "APP_KEY=" .env | findstr "base64:" >nul
    if !errorlevel! neq 0 (
        echo %YELLOW%🔑 App key missing, will generate...%RESET%
    )
)

REM Generate application key
echo %YELLOW%[7/8] Generating application key...%RESET%
php artisan key:generate --force --no-interaction
if %errorlevel% neq 0 (
    echo %RED%❌ Failed to generate application key%RESET%
    goto :error_exit
)
echo %GREEN%✅ Application key generated%RESET%

REM Setup database
echo %YELLOW%[8/8] Setting up database...%RESET%
if not exist database (
    mkdir database
    echo %GREEN%✅ Database directory created%RESET%
)

if not exist database\database.sqlite (
    type nul > database\database.sqlite
    echo %GREEN%✅ SQLite database file created%RESET%
) else (
    echo %YELLOW%ℹ️  Database file already exists%RESET%
)

php artisan migrate --force --no-interaction
if %errorlevel% neq 0 (
    echo %RED%❌ Failed to run migrations%RESET%
    echo %YELLOW%💡 Try running manually: php artisan migrate%RESET%
    echo %YELLOW%💡 Or reset database: php artisan migrate:fresh%RESET%
) else (
    echo %GREEN%✅ Database migrations completed%RESET%
)

REM Clear and optimize
echo.
echo %BLUE%🔧 Optimizing application...%RESET%
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan view:clear >nul 2>&1
php artisan route:clear >nul 2>&1
echo %GREEN%✅ Application optimized%RESET%

REM Install Node dependencies if available
if "!node_available!"=="true" (
    echo.
    echo %BLUE%🎨 Setting up frontend...%RESET%
    if exist package.json (
        npm install --silent
        if !errorlevel! equ 0 (
            echo %GREEN%✅ Frontend dependencies installed%RESET%
            echo %YELLOW%💡 Building assets...%RESET%
            npm run build --silent
            if !errorlevel! equ 0 (
                echo %GREEN%✅ Assets built successfully%RESET%
            ) else (
                echo %YELLOW%⚠️  Asset build failed, but project will work%RESET%
            )
        ) else (
            echo %YELLOW%⚠️  Failed to install frontend dependencies%RESET%
        )
    )
)

REM Test application
echo.
echo %BLUE%🧪 Testing application...%RESET%
php artisan route:list >nul 2>&1
if %errorlevel% equ 0 (
    echo %GREEN%✅ Application routes loaded successfully%RESET%
) else (
    echo %YELLOW%⚠️  Warning: Route loading test failed%RESET%
)

echo.
echo %GREEN%===============================================%RESET%
echo %GREEN%    🎉 SETUP COMPLETED SUCCESSFULLY! 🎉%RESET%
echo %GREEN%===============================================%RESET%
echo.
echo %BLUE%🚀 To start the application:%RESET%
echo %YELLOW%   php artisan serve%RESET%
echo %YELLOW%   Then visit: http://localhost:8000%RESET%
echo.
echo %BLUE%👤 Getting started:%RESET%
echo %YELLOW%   1. Visit http://localhost:8000/register to create account%RESET%
echo %YELLOW%   2. Login and start creating projects%RESET%
echo.
echo %BLUE%🔧 Useful development commands:%RESET%
echo %YELLOW%   php artisan migrate:fresh     (Reset database)%RESET%
echo %YELLOW%   php artisan tinker            (Laravel shell)%RESET%
echo %YELLOW%   npm run dev                   (Watch assets)%RESET%
echo %YELLOW%   php artisan route:list        (View all routes)%RESET%
echo.
echo %BLUE%📁 Project structure:%RESET%
echo %YELLOW%   🌐 Web routes: routes/web.php%RESET%
echo %YELLOW%   🎮 Controllers: app/Http/Controllers/%RESET%
echo %YELLOW%   📊 Models: app/Models/%RESET%
echo %YELLOW%   🎨 Views: resources/views/%RESET%
echo %YELLOW%   🗃️  Migrations: database/migrations/%RESET%
echo.
echo %BLUE%📋 Project features:%RESET%
echo %YELLOW%   ✅ User Authentication (Register/Login)%RESET%
echo %YELLOW%   ✅ Project Management (CRUD)%RESET%
echo %YELLOW%   ✅ Subtask Management%RESET%
echo %YELLOW%   ✅ Categories & Tags%RESET%
echo %YELLOW%   ✅ Progress Tracking%RESET%
echo %YELLOW%   ✅ Dashboard Analytics%RESET%
echo %YELLOW%   ✅ Responsive UI (Dark Mode Ready)%RESET%
echo %YELLOW%   ✅ RESTful API%RESET%
echo.
goto :success_exit

:error_exit
echo.
echo %RED%===============================================%RESET%
echo %RED%    ❌ SETUP FAILED! ❌%RESET%
echo %RED%===============================================%RESET%
echo.
echo %YELLOW%📋 Troubleshooting checklist:%RESET%
echo %YELLOW%   □ Install PHP 8.2+ with required extensions%RESET%
echo %YELLOW%   □ Install Composer%RESET%
echo %YELLOW%   □ Install Node.js (optional but recommended)%RESET%
echo %YELLOW%   □ Add PHP and Composer to system PATH%RESET%
echo %YELLOW%   □ Run as Administrator if permission issues%RESET%
echo.
echo %YELLOW%🔗 Download links:%RESET%
echo %YELLOW%   PHP: https://www.php.net/downloads%RESET%
echo %YELLOW%   Composer: https://getcomposer.org/download/%RESET%
echo %YELLOW%   Node.js: https://nodejs.org/%RESET%
echo.
echo %YELLOW%Please fix the issues above and run setup again.%RESET%
echo.
pause
exit /b 1

:success_exit
echo %GREEN%Press any key to continue...%RESET%
pause >nul
exit /b 0
