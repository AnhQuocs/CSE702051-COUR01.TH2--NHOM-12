<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Thêm indexes cho performance
            $table->index(['user_id', 'status']); // For filtering by user and status
            $table->index(['user_id', 'priority']); // For filtering by user and priority
            $table->index(['user_id', 'deadline']); // For filtering by user and deadline
            $table->index(['status', 'deadline']); // For overdue queries
            $table->index('reminder_time'); // For reminder command
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'priority']);
            $table->dropIndex(['user_id', 'deadline']);
            $table->dropIndex(['status', 'deadline']);
            $table->dropIndex(['reminder_time']);
        });
    }
};
