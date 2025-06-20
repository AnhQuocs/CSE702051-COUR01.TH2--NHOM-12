<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class CategoryTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo categories
        $categories = [
            [
                'name' => 'Công việc',
                'slug' => 'cong-viec',
                'description' => 'Các dự án liên quan đến công việc',
                'color' => '#3B82F6',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Cá nhân',
                'slug' => 'ca-nhan',
                'description' => 'Các dự án cá nhân',
                'color' => '#10B981',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Học tập',
                'slug' => 'hoc-tap',
                'description' => 'Các dự án học tập, nghiên cứu',
                'color' => '#F59E0B',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Sở thích',
                'slug' => 'so-thich',
                'description' => 'Các dự án liên quan đến sở thích',
                'color' => '#EF4444',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Tạo tags
        $tags = [
            ['name' => 'urgent', 'slug' => 'urgent', 'color' => '#EF4444'],
            ['name' => 'frontend', 'slug' => 'frontend', 'color' => '#3B82F6'],
            ['name' => 'backend', 'slug' => 'backend', 'color' => '#10B981'],
            ['name' => 'api', 'slug' => 'api', 'color' => '#F59E0B'],
            ['name' => 'database', 'slug' => 'database', 'color' => '#8B5CF6'],
            ['name' => 'mobile', 'slug' => 'mobile', 'color' => '#EC4899'],
            ['name' => 'web', 'slug' => 'web', 'color' => '#06B6D4'],
            ['name' => 'research', 'slug' => 'research', 'color' => '#84CC16'],
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(
                ['slug' => $tagData['slug']],
                array_merge($tagData, ['is_active' => true])
            );
        }

        $this->command->info('Created categories and tags successfully!');
    }
}
