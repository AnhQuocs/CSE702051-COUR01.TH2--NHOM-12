<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['Thấp', 'Trung bình', 'Cao'])->default('Trung bình');
            $table->enum('status', ['Lên kế hoạch', 'Đang thực hiện', 'Đã hoàn thành', 'Hoàn thành muộn'])->default('Lên kế hoạch');
            $table->boolean('completed_late')->default(false);
            $table->date('deadline')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
