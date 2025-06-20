<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // First, change columns to VARCHAR temporarily
            DB::statement("ALTER TABLE projects MODIFY COLUMN priority VARCHAR(20) DEFAULT 'medium'");
            DB::statement("ALTER TABLE projects MODIFY COLUMN status VARCHAR(20) DEFAULT 'not_started'");
        });
        
        // Update existing data to new values
        DB::table('projects')->where('priority', 'Thấp')->update(['priority' => 'low']);
        DB::table('projects')->where('priority', 'Trung bình')->update(['priority' => 'medium']);
        DB::table('projects')->where('priority', 'Cao')->update(['priority' => 'high']);
        
        DB::table('projects')->where('status', 'Lên kế hoạch')->update(['status' => 'not_started']);
        DB::table('projects')->where('status', 'Đang thực hiện')->update(['status' => 'in_progress']);
        DB::table('projects')->where('status', 'Đã hoàn thành')->update(['status' => 'completed']);
        DB::table('projects')->where('status', 'Hoàn thành muộn')->update(['status' => 'completed']);

        Schema::table('projects', function (Blueprint $table) {
            // Now change back to ENUM with new values
            DB::statement("ALTER TABLE projects MODIFY COLUMN priority ENUM('low', 'medium', 'high') DEFAULT 'medium'");
            DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('not_started', 'in_progress', 'completed', 'on_hold') DEFAULT 'not_started'");
            
            // Add new fields only if they don't exist
            if (!Schema::hasColumn('projects', 'start_date')) {
                $table->date('start_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('projects', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('projects', 'reminder_time')) {
                $table->datetime('reminder_time')->nullable()->after('end_date');
            }
            
            // Remove old fields only if they exist
            if (Schema::hasColumn('projects', 'deadline')) {
                $table->dropColumn('deadline');
            }
            if (Schema::hasColumn('projects', 'completed_late')) {
                $table->dropColumn('completed_late');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the changes
        Schema::table('projects', function (Blueprint $table) {
            // Add back old fields
            $table->date('deadline')->nullable();
            $table->boolean('completed_late')->default(false);
            
            // Remove new fields
            $table->dropColumn(['start_date', 'end_date', 'reminder_time']);
        });

        // Revert enum values
        DB::statement("ALTER TABLE projects MODIFY COLUMN priority ENUM('Thấp', 'Trung bình', 'Cao') DEFAULT 'Trung bình'");
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('Lên kế hoạch', 'Đang thực hiện', 'Đã hoàn thành', 'Hoàn thành muộn') DEFAULT 'Lên kế hoạch'");
        
        // Revert data
        DB::table('projects')->where('priority', 'low')->update(['priority' => 'Thấp']);
        DB::table('projects')->where('priority', 'medium')->update(['priority' => 'Trung bình']);
        DB::table('projects')->where('priority', 'high')->update(['priority' => 'Cao']);
        
        DB::table('projects')->where('status', 'not_started')->update(['status' => 'Lên kế hoạch']);
        DB::table('projects')->where('status', 'in_progress')->update(['status' => 'Đang thực hiện']);
        DB::table('projects')->where('status', 'completed')->update(['status' => 'Đã hoàn thành']);
        DB::table('projects')->where('status', 'on_hold')->update(['status' => 'Đang thực hiện']);
    }
};
