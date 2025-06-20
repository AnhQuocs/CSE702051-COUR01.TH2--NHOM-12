<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tạo dự án mới') }}
            </h2>
            <a href="{{ route('projects.index') }}" class="inline-block px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition">
                ← Quay lại danh sách
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề dự án *</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                            <select id="category_id" name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Mức độ ưu tiên *</label>
                            <select id="priority" name="priority" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Thấp</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="high" {{ old('priority', 'medium') == 'high' ? 'selected' : '' }}>Cao</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Trạng thái *</label>
                            <select id="status" name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="not_started" {{ old('status', 'not_started') == 'not_started' ? 'selected' : '' }}>Chưa bắt đầu</option>
                                <option value="in_progress" {{ old('status', 'not_started') == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                                <option value="completed" {{ old('status', 'not_started') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="on_hold" {{ old('status', 'not_started') == 'on_hold' ? 'selected' : '' }}>Tạm dừng</option>
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt đầu</label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Ngày kết thúc</label>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Tags -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach($tags as $tag)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                                               {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                               class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả dự án</label>
                            <textarea id="description" name="description" rows="4" 
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Mô tả chi tiết về dự án...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Reminder Time -->
                        <div class="md:col-span-2">
                            <label for="reminder_time" class="block text-sm font-medium text-gray-700 mb-2">Thời gian nhắc nhở</label>
                            <input type="datetime-local" id="reminder_time" name="reminder_time" value="{{ old('reminder_time') }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Hệ thống sẽ gửi email nhắc nhở vào thời gian này</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-6 space-x-3">
                        <a href="{{ route('projects.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                            Hủy
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Tạo dự án
                        </button>                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Date validation
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            const reminderTime = document.getElementById('reminder_time');
            const form = document.querySelector('form');

            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            startDate.setAttribute('min', today);
            endDate.setAttribute('min', today);

            // Validate end date when start date changes
            startDate.addEventListener('change', function() {
                if (this.value) {
                    endDate.setAttribute('min', this.value);
                    // Clear end date if it's before start date
                    if (endDate.value && endDate.value < this.value) {
                        endDate.value = '';
                    }
                }
            });

            // Validate reminder time when end date changes
            endDate.addEventListener('change', function() {
                if (this.value) {
                    const endDateTime = new Date(this.value + 'T23:59:59').toISOString().slice(0, 16);
                    reminderTime.setAttribute('max', endDateTime);
                    // Clear reminder time if it's after end date
                    if (reminderTime.value) {
                        const reminderDateTime = new Date(reminderTime.value);
                        const endDateObj = new Date(this.value + 'T23:59:59');
                        if (reminderDateTime >= endDateObj) {
                            reminderTime.value = '';
                        }
                    }
                }
            });

            // Form validation before submit
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessage = '';

                // Check if reminder time is before end date
                if (reminderTime.value && endDate.value) {
                    const reminderDateTime = new Date(reminderTime.value);
                    const endDateObj = new Date(endDate.value + 'T23:59:59');
                    
                    if (reminderDateTime >= endDateObj) {
                        isValid = false;
                        errorMessage = 'Thời gian nhắc nhở phải trước ngày kết thúc.';
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                }
            });
        });
    </script>
</x-app-layout>
