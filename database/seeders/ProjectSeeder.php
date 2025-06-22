<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\Category;
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

        // Tạo sample projects với subtasks
        $projectsData = [
            [
                'project' => [
                    'title' => 'Phát triển Website E-commerce',
                    'description' => 'Xây dựng website bán hàng trực tuyến với đầy đủ chức năng thanh toán, quản lý đơn hàng',
                    'priority' => 'high',
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays(30),
                    'reminder_time' => Carbon::now()->addDays(25),
                ],
                'subtasks' => [
                    ['title' => 'Thiết kế database', 'description' => 'Thiết kế cấu trúc cơ sở dữ liệu', 'is_completed' => true],
                    ['title' => 'Xây dựng API', 'description' => 'Phát triển API backend', 'is_completed' => true],
                    ['title' => 'Frontend user interface', 'description' => 'Thiết kế giao diện người dùng', 'is_completed' => false],
                    ['title' => 'Tích hợp thanh toán', 'description' => 'Tích hợp gateway thanh toán', 'is_completed' => false],
                ]
            ],
            [
                'project' => [
                    'title' => 'Ứng dụng Mobile Todo List',
                    'description' => 'Phát triển app mobile để quản lý công việc hàng ngày với React Native',
                    'priority' => 'medium',
                    'start_date' => Carbon::now()->addDays(5),
                    'end_date' => Carbon::now()->addDays(45),
                    'reminder_time' => Carbon::now()->addDays(40),
                ],
                'subtasks' => [
                    ['title' => 'Setup project React Native', 'description' => 'Khởi tạo dự án', 'is_completed' => false],
                    ['title' => 'Thiết kế wireframe', 'description' => 'Vẽ wireframe ứng dụng', 'is_completed' => false],
                ]
            ],
            [
                'project' => [
                    'title' => 'Hệ thống quản lý học sinh',
                    'description' => 'Xây dựng hệ thống quản lý thông tin học sinh, điểm số, học phí',
                    'priority' => 'high',
                    'start_date' => Carbon::now()->subDays(10),
                    'end_date' => Carbon::now()->addDays(60),
                    'reminder_time' => Carbon::now()->addDays(50),
                ],
                'subtasks' => [
                    ['title' => 'Phân tích yêu cầu', 'description' => 'Thu thập và phân tích yêu cầu', 'is_completed' => true],
                    ['title' => 'Thiết kế hệ thống', 'description' => 'Thiết kế kiến trúc hệ thống', 'is_completed' => true],
                    ['title' => 'Module quản lý học sinh', 'description' => 'Phát triển module học sinh', 'is_completed' => false],
                    ['title' => 'Module quản lý điểm', 'description' => 'Phát triển module điểm số', 'is_completed' => false],
                    ['title' => 'Module học phí', 'description' => 'Phát triển module học phí', 'is_completed' => false],
                ]
            ],
            [
                'project' => [
                    'title' => 'API REST cho blog',
                    'description' => 'Phát triển API backend cho hệ thống blog với Laravel',
                    'priority' => 'low',
                    'start_date' => Carbon::now()->subDays(30),
                    'end_date' => Carbon::now()->subDays(10),
                    'reminder_time' => null,
                ],
                'subtasks' => [
                    ['title' => 'Setup Laravel project', 'description' => 'Khởi tạo dự án Laravel', 'is_completed' => true],
                    ['title' => 'API Authentication', 'description' => 'Xây dựng hệ thống xác thực', 'is_completed' => true],
                    ['title' => 'Blog CRUD API', 'description' => 'API quản lý bài viết', 'is_completed' => true],
                    ['title' => 'Comment API', 'description' => 'API quản lý bình luận', 'is_completed' => true],
                ]
            ],
            [
                'project' => [
                    'title' => 'Chatbot AI customer service',
                    'description' => 'Tích hợp chatbot AI để hỗ trợ khách hàng tự động',
                    'priority' => 'high',
                    'start_date' => Carbon::now()->subDays(20),
                    'end_date' => Carbon::now()->subDays(5),
                    'reminder_time' => null,
                ],
                'subtasks' => [
                    ['title' => 'Research AI platforms', 'description' => 'Nghiên cứu các nền tảng AI', 'is_completed' => true],
                    ['title' => 'Training chatbot', 'description' => 'Huấn luyện chatbot', 'is_completed' => true],
                    ['title' => 'Integration', 'description' => 'Tích hợp vào website', 'is_completed' => true],
                    ['title' => 'Testing', 'description' => 'Kiểm thử hệ thống', 'is_completed' => true],
                ]
            ],
            [
                'project' => [
                    'title' => 'Dự án không có subtask',
                    'description' => 'Dự án này chưa lên kế hoạch chi tiết',
                    'priority' => 'medium',
                    'start_date' => null,
                    'end_date' => Carbon::now()->addDays(20),
                    'reminder_time' => null,
                ],
                'subtasks' => [] // Không có subtasks - sẽ có status "not_planned"
            ],
        ];

        foreach ($projectsData as $data) {
            // Lấy category ngẫu nhiên
            $category = Category::inRandomOrder()->first();
            
            $project = Project::create(array_merge($data['project'], [
                'user_id' => $user->id,
                'category_id' => $category ? $category->id : null
            ]));
            
            // Tạo subtasks nếu có
            foreach ($data['subtasks'] as $index => $subtaskData) {
                $project->subtasks()->create(array_merge($subtaskData, [
                    'order' => $index,
                ]));
            }
            
            // Gán tags ngẫu nhiên cho project
            $tags = \App\Models\Tag::where('is_active', true)->inRandomOrder()->take(rand(1, 3))->pluck('id');
            if ($tags->isNotEmpty()) {
                $project->tags()->attach($tags);
            }
        }

        $this->command->info('Created ' . count($projectsData) . ' sample projects with subtasks for user: ' . $user->email);
    }
}
