<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projects')->where('priority', 'low')->update(['priority' => 'Thấp']);
        DB::table('projects')->where('priority', 'medium')->update(['priority' => 'Trung bình']);
        DB::table('projects')->where('priority', 'high')->update(['priority' => 'Cao']);

        DB::table('projects')->where('status', 'not_started')->update(['status' => 'Lên kế hoạch']);
        DB::table('projects')->where('status', 'in_progress')->update(['status' => 'Đang thực hiện']);
        DB::table('projects')->where('status', 'completed')->update(['status' => 'Đã hoàn thành']);
        DB::table('projects')->where('status', 'on_hold')->update(['status' => 'Hoàn thành muộn']);

        // Xoá index nếu có, trước khi xoá cột
        if (Schema::hasColumn('projects', 'deadline')) {
            DB::statement('DROP INDEX IF EXISTS projects_status_deadline_index');
        }

        Schema::table('projects', function (Blueprint $table) {
    // Xóa các index liên quan đến 'deadline' trước
    DB::statement('DROP INDEX IF EXISTS projects_status_deadline_index');
    DB::statement('DROP INDEX IF EXISTS projects_user_id_deadline_index');

    // Xóa cột nếu tồn tại
    if (Schema::hasColumn('projects', 'deadline')) {
        $table->dropColumn('deadline');
    }

    if (Schema::hasColumn('projects', 'completed_late')) {
        $table->dropColumn('completed_late');
    }

    // Các cột mới
    if (!Schema::hasColumn('projects', 'start_date')) {
        $table->date('start_date')->nullable()->after('status');
    }
    if (!Schema::hasColumn('projects', 'end_date')) {
        $table->date('end_date')->nullable()->after('start_date');
    }
    if (!Schema::hasColumn('projects', 'reminder_time')) {
        $table->datetime('reminder_time')->nullable()->after('end_date');
    }
});

    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Thêm lại cột cũ
            $table->date('deadline')->nullable();
            $table->boolean('completed_late')->default(false);

            // Xoá các cột mới
            $table->dropColumn(['start_date', 'end_date', 'reminder_time']);

            // Đổi lại kiểu cột nếu muốn
            $table->string('priority', 20)->default('Trung bình')->change();
            $table->string('status', 20)->default('Lên kế hoạch')->change();
        });

        // Cập nhật dữ liệu về tiếng Việt
        DB::table('projects')->where('priority', 'low')->update(['priority' => 'Thấp']);
        DB::table('projects')->where('priority', 'medium')->update(['priority' => 'Trung bình']);
        DB::table('projects')->where('priority', 'high')->update(['priority' => 'Cao']);

        DB::table('projects')->where('status', 'not_started')->update(['status' => 'Lên kế hoạch']);
        DB::table('projects')->where('status', 'in_progress')->update(['status' => 'Đang thực hiện']);
        DB::table('projects')->where('status', 'completed')->update(['status' => 'Đã hoàn thành']);
        DB::table('projects')->where('status', 'on_hold')->update(['status' => 'Hoàn thành muộn']);
    }
};
