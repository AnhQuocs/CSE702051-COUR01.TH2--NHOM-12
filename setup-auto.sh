#!/bin/bash

# Laravel Project Auto Setup Script
# CSE702051-COUR01.TH2--NHOM-12
# Works on Linux/macOS/WSL

set -e  # Exit on any error

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RESET='\033[0m'

# Unicode symbols
SUCCESS="✅"
ERROR="❌"
WARNING="⚠️"
INFO="ℹ️"
ROCKET="🚀"

print_header() {
    echo "==============================================="
    echo "   $ROCKET LARAVEL PROJECT AUTO SETUP SCRIPT"
    echo "   CSE702051-COUR01.TH2--NHOM-12"
    echo "==============================================="
    echo
}

print_step() {
    echo -e "${YELLOW}[$1] $2${RESET}"
}

print_success() {
    echo -e "${GREEN}$SUCCESS $1${RESET}"
}

print_error() {
    echo -e "${RED}$ERROR $1${RESET}"
}

print_warning() {
    echo -e "${YELLOW}$WARNING $1${RESET}"
}

print_info() {
    echo -e "${BLUE}$INFO $1${RESET}"
}

check_command() {
    if command -v $1 &> /dev/null; then
        return 0
    else
        return 1
    fi
}

check_php_extension() {
    if php -m | grep -i "$1" &> /dev/null; then
        return 0
    else
        return 1
    fi
}

main() {
    print_header

    print_info "Checking system requirements..."
    echo

    # Check PHP
    print_step "1/8" "Checking PHP..."
    if check_command php; then
        php_version=$(php -r "echo PHP_VERSION;")
        if php -r "exit(version_compare(PHP_VERSION, '8.2.0', '<') ? 1 : 0);"; then
            print_success "PHP $php_version found"
        else
            print_error "PHP version $php_version is too old (required: 8.2+)"
            echo -e "${YELLOW}Install PHP 8.2+ from your package manager or https://www.php.net/${RESET}"
            exit 1
        fi
    else
        print_error "PHP is not installed"
        echo -e "${YELLOW}Install PHP 8.2+ from your package manager:${RESET}"
        echo -e "${YELLOW}  Ubuntu/Debian: sudo apt install php8.2-cli php8.2-*${RESET}"
        echo -e "${YELLOW}  macOS: brew install php${RESET}"
        exit 1
    fi

    # Check PHP extensions
    print_step "2/8" "Checking PHP extensions..."
    required_extensions=("pdo" "sqlite3" "openssl" "mbstring" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo")
    missing_extensions=()

    for ext in "${required_extensions[@]}"; do
        if check_php_extension "$ext"; then
            print_success "$ext extension found"
        else
            print_error "Missing PHP extension: $ext"
            missing_extensions+=($ext)
        fi
    done

    if [ ${#missing_extensions[@]} -ne 0 ]; then
        print_error "Missing required PHP extensions: ${missing_extensions[*]}"
        echo -e "${YELLOW}Install missing extensions:${RESET}"
        echo -e "${YELLOW}  Ubuntu/Debian: sudo apt install php8.2-{${missing_extensions[*]}}${RESET}"
        echo -e "${YELLOW}  macOS: Extensions usually included with Homebrew PHP${RESET}"
        exit 1
    fi

    # Check Composer
    print_step "3/8" "Checking Composer..."
    if check_command composer; then
        composer_version=$(composer --version --no-ansi 2>/dev/null | grep -oE '[0-9]+\.[0-9]+\.[0-9]+' | head -1)
        print_success "Composer $composer_version found"
    else
        print_error "Composer is not installed"
        echo -e "${YELLOW}Install Composer:${RESET}"
        echo -e "${YELLOW}  curl -sS https://getcomposer.org/installer | php${RESET}"
        echo -e "${YELLOW}  sudo mv composer.phar /usr/local/bin/composer${RESET}"
        exit 1
    fi

    # Check Node.js
    print_step "4/8" "Checking Node.js..."
    if check_command node; then
        node_version=$(node --version)
        print_success "Node.js $node_version found"
        node_available=true
    else
        print_warning "Node.js not found (optional for frontend assets)"
        echo -e "${YELLOW}Install Node.js: https://nodejs.org/${RESET}"
        node_available=false
    fi

    echo
    print_info "Installing dependencies..."
    echo

    # Install Composer dependencies
    print_step "5/8" "Installing PHP dependencies..."
    if composer install --no-dev --optimize-autoloader --no-interaction; then
        print_success "PHP dependencies installed"
    else
        print_error "Failed to install Composer dependencies"
        exit 1
    fi

    # Setup environment
    print_step "6/8" "Setting up environment..."
    if [ ! -f .env ]; then
        if [ -f .env.example ]; then
            cp .env.example .env
            print_success "Environment file created from .env.example"
        else
            print_error ".env.example file not found"
            exit 1
        fi
    else
        print_warning ".env file already exists"
    fi

    # Generate application key
    print_step "7/8" "Generating application key..."
    if php artisan key:generate --force --no-interaction; then
        print_success "Application key generated"
    else
        print_error "Failed to generate application key"
        exit 1
    fi

    # Setup database
    print_step "8/8" "Setting up database..."
    mkdir -p database
    
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
        print_success "SQLite database file created"
    else
        print_info "Database file already exists"
    fi

    if php artisan migrate --force --no-interaction; then
        print_success "Database migrations completed"
    else
        print_error "Failed to run migrations"
        echo -e "${YELLOW}Try running manually: php artisan migrate${RESET}"
    fi

    # Clear and optimize
    echo
    print_info "Optimizing application..."
    php artisan config:clear &>/dev/null
    php artisan cache:clear &>/dev/null
    php artisan view:clear &>/dev/null
    php artisan route:clear &>/dev/null
    print_success "Application optimized"

    # Install Node dependencies
    if [ "$node_available" = true ] && [ -f package.json ]; then
        echo
        print_info "Setting up frontend..."
        if npm install --silent; then
            print_success "Frontend dependencies installed"
            if npm run build --silent; then
                print_success "Assets built successfully"
            else
                print_warning "Asset build failed, but project will work"
            fi
        else
            print_warning "Failed to install frontend dependencies"
        fi
    fi

    # Test application
    echo
    print_info "Testing application..."
    if php artisan route:list &>/dev/null; then
        print_success "Application routes loaded successfully"
    else
        print_warning "Route loading test failed"
    fi

    # Success message
    echo
    echo -e "${GREEN}===============================================${RESET}"
    echo -e "${GREEN}    🎉 SETUP COMPLETED SUCCESSFULLY! 🎉${RESET}"
    echo -e "${GREEN}===============================================${RESET}"
    echo
    echo -e "${BLUE}$ROCKET To start the application:${RESET}"
    echo -e "${YELLOW}   php artisan serve${RESET}"
    echo -e "${YELLOW}   Then visit: http://localhost:8000${RESET}"
    echo
    echo -e "${BLUE}👤 Getting started:${RESET}"
    echo -e "${YELLOW}   1. Visit http://localhost:8000/register to create account${RESET}"
    echo -e "${YELLOW}   2. Login and start creating projects${RESET}"
    echo
    echo -e "${BLUE}🔧 Useful commands:${RESET}"
    echo -e "${YELLOW}   php artisan migrate:fresh     (Reset database)${RESET}"
    echo -e "${YELLOW}   php artisan tinker            (Laravel shell)${RESET}"
    echo -e "${YELLOW}   npm run dev                   (Watch assets)${RESET}"
    echo -e "${YELLOW}   php artisan route:list        (View routes)${RESET}"
    echo
    echo -e "${BLUE}📁 Project features:${RESET}"
    echo -e "${YELLOW}   $SUCCESS User Authentication${RESET}"
    echo -e "${YELLOW}   $SUCCESS Project Management${RESET}"
    echo -e "${YELLOW}   $SUCCESS Subtask Management${RESET}"
    echo -e "${YELLOW}   $SUCCESS Progress Tracking${RESET}"
    echo -e "${YELLOW}   $SUCCESS Dashboard Analytics${RESET}"
    echo -e "${YELLOW}   $SUCCESS RESTful API${RESET}"
    echo
}

# Error handling
handle_error() {
    echo
    echo -e "${RED}===============================================${RESET}"
    echo -e "${RED}    $ERROR SETUP FAILED! $ERROR${RESET}"
    echo -e "${RED}===============================================${RESET}"
    echo
    echo -e "${YELLOW}Please check the error messages above and try again.${RESET}"
    exit 1
}

trap 'handle_error' ERR

# Run main function
main "$@"
