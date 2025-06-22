<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Chỉnh sửa dự án
        </h2>
            <a href="{{ route('projects.show', $project) }}" class="inline-block px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition">
                ← Quay lại
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

                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề dự án *</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $project->title) }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                            <select id="category_id" name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $project->category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Mức độ ưu tiên *</label>
                            <select id="priority" name="priority" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>Thấp</option>
                                <option value="medium" {{ old('priority', $project->priority) == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>Cao</option>
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt đầu</label>
                            <input type="date" id="start_date" name="start_date" 
                                   value="{{ old('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Ngày kết thúc</label>
                            <input type="date" id="end_date" name="end_date" 
                                   value="{{ old('end_date', $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '') }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>                        <!-- Nhãn -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nhãn</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach($tags as $tag)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                                               {{ in_array($tag->id, old('tags', $projectTags)) ? 'checked' : '' }}
                                               class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả dự án</label>
                            <textarea id="description" name="description" rows="4" 
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Mô tả chi tiết về dự án...">{{ old('description', $project->description) }}</textarea>
                        </div>

                        <!-- Subtasks -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Công việc cần làm</label>
                            <div id="subtasks-container" class="space-y-3">
                                @php
                                    $oldSubtasks = old('subtasks', $project->subtasks->toArray());
                                @endphp
                                
                                @if(count($oldSubtasks) > 0)
                                    @foreach($oldSubtasks as $index => $subtask)
                                        <div class="subtask-item border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-1 space-y-3">
                                                    <input type="text" 
                                                           name="subtasks[{{ $index }}][title]" 
                                                           value="{{ is_array($subtask) ? ($subtask['title'] ?? '') : $subtask->title }}"
                                                           placeholder="Tiêu đề công việc..."
                                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <textarea name="subtasks[{{ $index }}][description]" 
                                                              rows="2"
                                                              placeholder="Mô tả công việc (tùy chọn)..."
                                                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ is_array($subtask) ? ($subtask['description'] ?? '') : $subtask->description }}</textarea>
                                                    <label class="flex items-center">
                                                        <input type="checkbox" 
                                                               name="subtasks[{{ $index }}][is_completed]" 
                                                               value="1"
                                                               {{ (is_array($subtask) ? ($subtask['is_completed'] ?? false) : $subtask->is_completed) ? 'checked' : '' }}
                                                               class="mr-2 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                        <span class="text-sm text-gray-700">Đã hoàn thành</span>
                                                    </label>
                                                </div>
                                                <button type="button" onclick="removeSubtask(this)" class="text-red-600 hover:text-red-800">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="subtask-item border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-1 space-y-3">
                                                <input type="text" 
                                                       name="subtasks[0][title]" 
                                                       placeholder="Tiêu đề công việc..."
                                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <textarea name="subtasks[0][description]" 
                                                          rows="2"
                                                          placeholder="Mô tả công việc (tùy chọn)..."
                                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                                <label class="flex items-center">
                                                    <input type="checkbox" 
                                                           name="subtasks[0][is_completed]" 
                                                           value="1"
                                                           class="mr-2 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                    <span class="text-sm text-gray-700">Đã hoàn thành</span>
                                                </label>
                                            </div>
                                            <button type="button" onclick="removeSubtask(this)" class="text-red-600 hover:text-red-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="addSubtask()" class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Thêm công việc
                            </button>
                            <p class="text-xs text-gray-500 mt-2">Trạng thái dự án sẽ được tự động cập nhật dựa trên tiến độ hoàn thành các công việc</p>
                        </div>

                        <!-- Reminder Time -->
                        <div class="md:col-span-2">
                            <label for="reminder_time" class="block text-sm font-medium text-gray-700 mb-2">Thời gian nhắc nhở</label>
                            <input type="datetime-local" id="reminder_time" name="reminder_time" 
                                   value="{{ old('reminder_time', $project->reminder_time ? \Carbon\Carbon::parse($project->reminder_time)->format('Y-m-d\TH:i') : '') }}" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Hệ thống sẽ gửi email nhắc nhở vào thời gian này</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-6 space-x-3">
                        <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                            Hủy
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Cập nhật dự án                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>    <script>
        let subtaskIndex = {{ count(old('subtasks', $project->subtasks->toArray())) }};

        // Add new subtask
        function addSubtask() {
            const container = document.getElementById('subtasks-container');
            const subtaskHtml = `
                <div class="subtask-item border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-1 space-y-3">
                            <input type="text" 
                                   name="subtasks[${subtaskIndex}][title]" 
                                   placeholder="Tiêu đề công việc..."
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <textarea name="subtasks[${subtaskIndex}][description]" 
                                      rows="2"
                                      placeholder="Mô tả công việc (tùy chọn)..."
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="subtasks[${subtaskIndex}][is_completed]" 
                                       value="1"
                                       class="mr-2 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700">Đã hoàn thành</span>
                            </label>
                        </div>
                        <button type="button" onclick="removeSubtask(this)" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', subtaskHtml);
            subtaskIndex++;
        }

        // Remove subtask
        function removeSubtask(button) {
            const container = document.getElementById('subtasks-container');
            if (container.children.length > 1) {
                button.closest('.subtask-item').remove();
            } else {
                // Clear the inputs instead of removing if it's the last one
                const inputs = button.closest('.subtask-item').querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });
            }
        }

        // Date validation for edit form
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            const reminderTime = document.getElementById('reminder_time');

            // Update minimum dates based on current values
            function updateDateConstraints() {
                if (startDate.value) {
                    endDate.setAttribute('min', startDate.value);
                }
                if (endDate.value) {
                    const endDateTime = new Date(endDate.value + 'T23:59:59').toISOString().slice(0, 16);
                    reminderTime.setAttribute('max', endDateTime);
                }
            }
            
            // Initialize constraints
            updateDateConstraints();

            startDate.addEventListener('change', function() {
                if (this.value) {
                    endDate.setAttribute('min', this.value);
                    if (endDate.value && endDate.value < this.value) {
                        endDate.value = '';
                    }
                }
            });

            // Validate reminder time
            function validateReminderTime() {
                if (reminderTime.value && endDate.value) {
                    const reminderDate = new Date(reminderTime.value);
                    const endDateTime = new Date(endDate.value + 'T23:59:59');
                    
                    if (reminderDate >= endDateTime) {
                        alert('Thời gian nhắc nhở phải trước ngày kết thúc');
                        reminderTime.value = '';
                    }
                }
            }

            reminderTime.addEventListener('change', validateReminderTime);
            endDate.addEventListener('change', validateReminderTime);
        });
    </script>
</x-app-layout>
