<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== KIỂM TRA DATABASE STRUCTURE ===\n\n";

// Lấy danh sách bảng
$tables = DB::select('SHOW TABLES');
echo "Các bảng trong database hiện tại:\n";
foreach($tables as $table) {
    $tableArray = (array) $table;
    $tableName = array_values($tableArray)[0];
    echo "- $tableName\n";
}

echo "\n=== CHI TIẾT CẤU TRÚC BẢNG ===\n";

// Danh sách bảng ứng dụng (không bao gồm system tables)
$appTables = [
    'users', 'projects', 'categories', 'tags', 'project_tag', 
    'subtasks', 'personal_access_tokens'
];

foreach($appTables as $tableName) {
    echo "\n🔍 Bảng: $tableName\n";
    try {
        $columns = DB::select("DESCRIBE $tableName");
        foreach($columns as $column) {
            echo "  - {$column->Field} ({$column->Type}) {$column->Null} {$column->Key} {$column->Default}\n";
        }
        
        // Đếm số bản ghi
        $count = DB::table($tableName)->count();
        echo "  📊 Số bản ghi: $count\n";
        
    } catch(Exception $e) {
        echo "  ❌ Lỗi: " . $e->getMessage() . "\n";
    }
}

echo "\n=== KIỂM TRA RELATIONSHIPS ===\n";

// Kiểm tra foreign keys
echo "\n🔗 Foreign Keys:\n";
$fks = DB::select("
    SELECT 
        TABLE_NAME,
        COLUMN_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM 
        information_schema.KEY_COLUMN_USAGE 
    WHERE 
        REFERENCED_TABLE_SCHEMA = DATABASE()
        AND REFERENCED_TABLE_NAME IS NOT NULL
");

foreach($fks as $fk) {
    echo "  - {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
}

echo "\n=== KIỂM TRA INDEXES ===\n";
foreach($appTables as $tableName) {
    echo "\n📋 Indexes cho $tableName:\n";
    try {
        $indexes = DB::select("SHOW INDEX FROM $tableName");
        foreach($indexes as $index) {
            echo "  - {$index->Key_name} ({$index->Column_name}) - {$index->Index_type}\n";
        }
    } catch(Exception $e) {
        echo "  ❌ Lỗi: " . $e->getMessage() . "\n";
    }
}
