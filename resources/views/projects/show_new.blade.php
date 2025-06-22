<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $project->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('dashboard') }}" class="inline-block px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition">
                    ← Quay lại Dashboard
                </a>
                <a href="{{ route('projects.index') }}" class="inline-block px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition">
                    Quản lý dự án
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Progress Bar and Status Section -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tiến độ dự án</h3>
                    
                    <!-- Status Badge -->
                    @php
                        $statusColors = [
                            'not_planned' => 'bg-gray-100 text-gray-800',
                            'not_started' => 'bg-yellow-100 text-yellow-800',
                            'in_progress' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'overdue' => 'bg-red-100 text-red-800'
                        ];
                        $statusLabels = [
                            'not_planned' => 'Chưa lên kế hoạch',
                            'not_started' => 'Chưa bắt đầu',
                            'in_progress' => 'Đang thực hiện',
                            'completed' => 'Hoàn thành',
                            'overdue' => 'Quá hạn'
                        ];
                    @endphp
                    <span class="inline-flex px-4 py-2 text-sm font-medium rounded-full {{ $statusColors[$project->final_status] ?? 'bg-gray-100 text-gray-800' }}" id="project-status">
                        {{ $statusLabels[$project->final_status] ?? $project->final_status }}
                    </span>
                    
                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-600">Tiến độ hoàn thành</span>
                            <span class="font-medium text-gray-900" id="progress-text">{{ $project->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                                 id="progress-bar"
                                 style="width: {{ $project->progress_percentage }}%"></div>
                        </div>
                        @if($project->subtasks->count() > 0)
                            <p class="text-sm text-gray-500 mt-2" id="progress-stats">
                                {{ $project->subtasks->where('is_completed', true)->count() }}/{{ $project->subtasks->count() }} công việc đã hoàn thành
                            </p>
                        @else
                            <p class="text-sm text-gray-500 mt-2">Chưa có công việc nào được tạo</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Subtasks Management Section -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Quản lý công việc</h3>
                    <button onclick="showAddSubtaskForm()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Thêm công việc
                    </button>
                </div>

                <!-- Add Subtask Form (Hidden by default) -->
                <div id="add-subtask-form" class="hidden mb-6 p-4 bg-gray-50 rounded-lg border">
                    <h4 class="font-medium text-gray-900 mb-3">Thêm công việc mới</h4>
                    <form id="subtask-form">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="subtask-title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề công việc *</label>
                                <input type="text" id="subtask-title" name="title" required 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="subtask-description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả (tùy chọn)</label>
                                <textarea id="subtask-description" name="description" rows="2"
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="hideAddSubtaskForm()" 
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                                Hủy
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                Thêm công việc
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Subtasks List -->
                <div id="subtasks-container">
                    @if($project->subtasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($project->subtasks as $subtask)
                                <div class="subtask-item flex items-center justify-between p-4 bg-gray-50 rounded-lg border" 
                                     data-subtask-id="{{ $subtask->id }}">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <input type="checkbox" 
                                               {{ $subtask->is_completed ? 'checked' : '' }}
                                               onchange="toggleSubtask({{ $subtask->id }})"
                                               class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        <div class="flex-1">
                                            <div class="font-medium {{ $subtask->is_completed ? 'line-through text-gray-500' : 'text-gray-900' }}">
                                                {{ $subtask->title }}
                                            </div>
                                            @if($subtask->description)
                                                <div class="text-sm text-gray-500 mt-1 {{ $subtask->is_completed ? 'line-through' : '' }}">
                                                    {{ $subtask->description }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <button onclick="deleteSubtask({{ $subtask->id }})" 
                                            class="ml-3 text-red-600 hover:text-red-800 p-1"
                                            title="Xóa công việc">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>Chưa có công việc nào được tạo</p>
                            <p class="text-sm">Bấm "Thêm công việc" để bắt đầu</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Project Information -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Thông tin dự án</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Left Column -->
                    <div>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Mức độ ưu tiên:</span>
                                @php
                                    $priorityColors = [
                                        'low' => 'text-green-600',
                                        'medium' => 'text-yellow-600',
                                        'high' => 'text-red-600'
                                    ];
                                    $priorityLabels = [
                                        'low' => 'Thấp',
                                        'medium' => 'Trung bình',
                                        'high' => 'Cao'
                                    ];
                                @endphp
                                <span class="ml-2 font-medium {{ $priorityColors[$project->priority] ?? 'text-gray-600' }}">
                                    {{ $priorityLabels[$project->priority] ?? $project->priority }}
                                </span>
                            </div>
                            
                            @if($project->category)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Danh mục:</span>
                                    <span class="ml-2 px-2 py-1 text-sm rounded" 
                                          style="background-color: {{ $project->category->color }}20; color: {{ $project->category->color }}">
                                        {{ $project->category->name }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($project->tags->count() > 0)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Nhãn:</span>
                                    <div class="mt-1">
                                        @foreach($project->tags as $tag)
                                            <span class="inline-block text-sm px-2 py-1 rounded mr-2 mb-1"
                                                  style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                #{{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div>
                        <div class="space-y-3">
                            @if($project->start_date)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Ngày bắt đầu:</span>
                                    <span class="ml-2 text-gray-900">{{ \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            
                            @if($project->end_date)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Ngày kết thúc:</span>
                                    <span class="ml-2 text-gray-900">{{ \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            
                            <div>
                                <span class="text-sm font-medium text-gray-500">Ngày tạo:</span>
                                <span class="ml-2 text-gray-900">{{ $project->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($project->description)
                    <div class="mt-6">
                        <span class="text-sm font-medium text-gray-500">Mô tả dự án:</span>
                        <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $project->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Status colors and labels
        const statusColors = {
            'not_planned': 'bg-gray-100 text-gray-800',
            'not_started': 'bg-yellow-100 text-yellow-800',
            'in_progress': 'bg-blue-100 text-blue-800',
            'completed': 'bg-green-100 text-green-800',
            'overdue': 'bg-red-100 text-red-800'
        };
        
        const statusLabels = {
            'not_planned': 'Chưa lên kế hoạch',
            'not_started': 'Chưa bắt đầu',
            'in_progress': 'Đang thực hiện',
            'completed': 'Hoàn thành',
            'overdue': 'Quá hạn'
        };

        // Show/Hide Add Subtask Form
        function showAddSubtaskForm() {
            document.getElementById('add-subtask-form').classList.remove('hidden');
            document.getElementById('subtask-title').focus();
        }
        
        function hideAddSubtaskForm() {
            document.getElementById('add-subtask-form').classList.add('hidden');
            document.getElementById('subtask-form').reset();
        }

        // Add new subtask
        document.getElementById('subtask-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(`/projects/{{ $project->id }}/subtasks`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Simple reload to update the UI
                } else {
                    alert('Có lỗi xảy ra khi thêm công việc!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
        });

        // Toggle subtask completion with auto-save
        function toggleSubtask(subtaskId) {
            const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
            const checkbox = subtaskItem.querySelector('input[type="checkbox"]');
            const titleDiv = subtaskItem.querySelector('.font-medium');
            const descDiv = subtaskItem.querySelector('.text-sm.text-gray-500');
            
            fetch(`/subtasks/${subtaskId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update checkbox state
                    checkbox.checked = data.subtask.is_completed;
                    
                    // Update text styling
                    if (data.subtask.is_completed) {
                        titleDiv.classList.add('line-through', 'text-gray-500');
                        titleDiv.classList.remove('text-gray-900');
                        if (descDiv) {
                            descDiv.classList.add('line-through');
                        }
                    } else {
                        titleDiv.classList.remove('line-through', 'text-gray-500');
                        titleDiv.classList.add('text-gray-900');
                        if (descDiv) {
                            descDiv.classList.remove('line-through');
                        }
                    }
                    
                    // Update progress bar and status
                    updateProgressAndStatus(data.project);
                    
                    console.log('Subtask toggled successfully');
                } else {
                    // Revert checkbox if failed
                    checkbox.checked = !checkbox.checked;
                    alert('Có lỗi xảy ra khi cập nhật trạng thái công việc!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert checkbox if failed
                checkbox.checked = !checkbox.checked;
                alert('Có lỗi xảy ra!');
            });
        }

        // Update progress bar and status
        function updateProgressAndStatus(projectData) {
            // Update progress bar
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const progressStats = document.getElementById('progress-stats');
            const projectStatus = document.getElementById('project-status');
            
            if (progressBar && progressText) {
                progressBar.style.width = projectData.progress_percentage + '%';
                progressText.textContent = projectData.progress_percentage + '%';
            }
            
            if (progressStats) {
                progressStats.textContent = `${projectData.completed_subtasks}/${projectData.total_subtasks} công việc đã hoàn thành`;
            }
            
            // Update status badge
            if (projectStatus) {
                projectStatus.className = `inline-flex px-4 py-2 text-sm font-medium rounded-full ${statusColors[projectData.final_status] || 'bg-gray-100 text-gray-800'}`;
                projectStatus.textContent = statusLabels[projectData.final_status] || projectData.final_status;
            }
        }

        // Delete subtask
        function deleteSubtask(subtaskId) {
            if (!confirm('Bạn có chắc chắn muốn xóa công việc này?')) {
                return;
            }
            
            fetch(`/subtasks/${subtaskId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Simple reload to update the UI
                } else {
                    alert('Có lỗi xảy ra khi xóa công việc!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra!');
            });
        }
    </script>
</x-app-layout>
