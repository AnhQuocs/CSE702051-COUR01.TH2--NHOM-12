<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo user test nếu chưa có
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Tạo sample projects
        $projects = [
            [
                'title' => 'Phát triển Website E-commerce',
                'description' => 'Xây dựng website bán hàng trực tuyến với đầy đủ chức năng thanh toán, quản lý đơn hàng',
                'priority' => 'Cao',
                'status' => 'Đang thực hiện',
                'deadline' => Carbon::now()->addDays(30),
                'reminder_time' => Carbon::now()->addDays(25),
            ],
            [
                'title' => 'Ứng dụng Mobile Todo List',
                'description' => 'Phát triển app mobile để quản lý công việc hàng ngày với React Native',
                'priority' => 'Trung bình',
                'status' => 'Lên kế hoạch',
                'deadline' => Carbon::now()->addDays(45),
                'reminder_time' => Carbon::now()->addDays(40),
            ],
            [
                'title' => 'Hệ thống quản lý học sinh',
                'description' => 'Xây dựng hệ thống quản lý thông tin học sinh, điểm số, học phí',
                'priority' => 'Cao',
                'status' => 'Đang thực hiện',
                'deadline' => Carbon::now()->addDays(60),
                'reminder_time' => Carbon::now()->addDays(50),
            ],
            [
                'title' => 'API REST cho blog',
                'description' => 'Phát triển API backend cho hệ thống blog với Laravel',
                'priority' => 'Thấp',
                'status' => 'Đã hoàn thành',
                'deadline' => Carbon::now()->subDays(10),
                'completed_late' => false,
            ],
            [
                'title' => 'Chatbot AI customer service',
                'description' => 'Tích hợp chatbot AI để hỗ trợ khách hàng tự động',
                'priority' => 'Cao',
                'status' => 'Hoàn thành muộn',
                'deadline' => Carbon::now()->subDays(5),
                'completed_late' => true,
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create(array_merge($projectData, ['user_id' => $user->id]));
        }

        $this->command->info('Created ' . count($projects) . ' sample projects for user: ' . $user->email);
    }
}
