<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $project->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('projects.edit', $project) }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Chỉnh sửa
                </a>
                <a href="{{ route('projects.index') }}" class="inline-block px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition">
                    ← Quay lại
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Project Details -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Left Column -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Chi tiết dự án</h3>
                        
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Trạng thái:</span>                                @php
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
                                <span class="ml-2 px-3 py-1 text-sm font-medium rounded-full {{ $statusColors[$project->final_status] ?? 'bg-gray-100 text-gray-800' }}" id="project-status">
                                    {{ $statusLabels[$project->final_status] ?? $project->final_status }}
                                </span>
                            </div>
                            
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
                                    <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 text-sm rounded">
                                        {{ $project->category->name }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($project->tags->count() > 0)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Tags:</span>
                                    <div class="mt-1">
                                        @foreach($project->tags as $tag)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded mr-2 mb-1">
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
                        <h3 class="text-lg font-semibold mb-4">Thời gian</h3>
                        
                        <div class="space-y-3">
                            @if($project->start_date)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Ngày bắt đầu:</span>
                                    <span class="ml-2">{{ \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            
                            @if($project->end_date)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Ngày kết thúc:</span>
                                    <span class="ml-2">{{ \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            
                            @if($project->reminder_time)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Thời gian nhắc nhở:</span>
                                    <span class="ml-2">{{ \Carbon\Carbon::parse($project->reminder_time)->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                            
                            <div>
                                <span class="text-sm font-medium text-gray-500">Ngày tạo:</span>
                                <span class="ml-2">{{ $project->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            
                            <div>
                                <span class="text-sm font-medium text-gray-500">Cập nhật cuối:</span>
                                <span class="ml-2">{{ $project->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                @if($project->description)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-2">Mô tả</h3>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 leading-relaxed">{{ $project->description }}</p>
                        </div>
                    </div>                @endif
            </div>
            
            <!-- Subtasks Section -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold">Công việc con</h3>
                        @if($project->subtasks->count() > 0)
                            <div class="mt-2 flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-3">
                                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                                         style="width: {{ $project->progress_percentage }}%" id="progress-bar"></div>
                                </div>
                                <span class="text-sm text-gray-600" id="progress-text">{{ $project->progress_percentage }}%</span>
                            </div>
                        @endif
                    </div>
                    <button onclick="showAddSubtaskForm()" 
                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Thêm công việc
                    </button>
                </div>

                <!-- Add Subtask Form (Hidden by default) -->
                <div id="add-subtask-form" class="hidden mb-4 p-4 bg-gray-50 rounded-lg border">
                    <h4 class="font-medium mb-3">Thêm công việc mới</h4>
                    <form id="subtask-form">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                                <input type="text" name="title" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                                <input type="text" name="description" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                        <div class="mt-3 flex space-x-2">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm">
                                Thêm
                            </button>
                            <button type="button" onclick="hideAddSubtaskForm()" 
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition text-sm">
                                Hủy
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Subtasks List -->
                <div id="subtasks-container">
                    @if($project->subtasks->count() > 0)
                        @foreach($project->subtasks as $subtask)
                            <div class="subtask-item flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2" 
                                 data-subtask-id="{{ $subtask->id }}">
                                <div class="flex items-center flex-1">
                                    <input type="checkbox" 
                                           {{ $subtask->is_completed ? 'checked' : '' }}
                                           onchange="toggleSubtask({{ $subtask->id }})"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mr-3">
                                    <div class="flex-1">
                                        <div class="font-medium {{ $subtask->is_completed ? 'line-through text-gray-500' : 'text-gray-900' }}">
                                            {{ $subtask->title }}
                                        </div>
                                        @if($subtask->description)
                                            <div class="text-sm text-gray-600 mt-1">{{ $subtask->description }}</div>
                                        @endif
                                    </div>
                                </div>
                                <button onclick="deleteSubtask({{ $subtask->id }})" 
                                        class="ml-2 p-1 text-red-600 hover:text-red-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div id="no-subtasks" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <p>Chưa có công việc nào. Nhấn "Thêm công việc" để bắt đầu!</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Comments Section -->
            @if($project->comments && $project->comments->count() > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Bình luận ({{ $project->comments->count() }})</h3>
                    
                    <div class="space-y-4">
                        @foreach($project->comments as $comment)
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-gray-900">{{ $comment->user->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-gray-700">{{ $comment->content }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="mt-6 flex space-x-4">
                <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Chỉnh sửa dự án
                </a>
                
                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline" 
                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Xóa dự án
                    </button>
                </form>
            </div>        </div>
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

        // Show add subtask form
        function showAddSubtaskForm() {
            document.getElementById('add-subtask-form').classList.remove('hidden');
            document.querySelector('#add-subtask-form input[name="title"]').focus();
        }

        // Hide add subtask form
        function hideAddSubtaskForm() {
            document.getElementById('add-subtask-form').classList.add('hidden');
            document.getElementById('subtask-form').reset();
        }

        // Toggle subtask completion
        function toggleSubtask(subtaskId) {
            fetch(`/subtasks/${subtaskId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update progress bar
                    updateProgressBar(data.progress);
                    // Update status
                    updateProjectStatus(data.status);
                    // Update subtask appearance
                    updateSubtaskAppearance(subtaskId, data.is_completed);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert checkbox state on error
                const checkbox = document.querySelector(`[data-subtask-id="${subtaskId}"] input[type="checkbox"]`);
                checkbox.checked = !checkbox.checked;
            });
        }

        // Delete subtask
        function deleteSubtask(subtaskId) {
            if (confirm('Bạn có chắc chắn muốn xóa công việc này?')) {
                fetch(`/subtasks/${subtaskId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove subtask from DOM
                        document.querySelector(`[data-subtask-id="${subtaskId}"]`).remove();
                        // Update progress bar
                        updateProgressBar(data.progress);
                        // Update status
                        updateProjectStatus(data.status);
                        // Check if no subtasks left
                        checkIfNoSubtasks();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa công việc.');
                });
            }
        }

        // Add new subtask
        document.getElementById('subtask-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            fetch(`/projects/{{ $project->id }}/subtasks`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide no-subtasks message if it exists
                    const noSubtasks = document.getElementById('no-subtasks');
                    if (noSubtasks) {
                        noSubtasks.remove();
                    }
                    
                    // Add new subtask to DOM
                    addSubtaskToDOM(data.subtask);
                    // Update progress bar
                    updateProgressBar(data.progress);
                    // Update status
                    updateProjectStatus(data.status);
                    // Hide form and reset
                    hideAddSubtaskForm();
                    // Show progress bar if this is the first subtask
                    showProgressBarIfNeeded();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm công việc.');
            });
        });

        // Helper functions
        function updateProgressBar(progress) {
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            if (progressBar && progressText) {
                progressBar.style.width = progress + '%';
                progressText.textContent = progress + '%';
            }
        }

        function updateProjectStatus(status) {
            const statusElement = document.getElementById('project-status');
            if (statusElement) {
                // Remove all existing status classes
                Object.values(statusColors).forEach(className => {
                    statusElement.classList.remove(...className.split(' '));
                });
                // Add new status classes
                const newClasses = statusColors[status] || 'bg-gray-100 text-gray-800';
                statusElement.classList.add(...newClasses.split(' '));
                // Update text
                statusElement.textContent = statusLabels[status] || status;
            }
        }

        function updateSubtaskAppearance(subtaskId, isCompleted) {
            const subtaskElement = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
            const titleElement = subtaskElement.querySelector('.font-medium');
            
            if (isCompleted) {
                titleElement.classList.add('line-through', 'text-gray-500');
                titleElement.classList.remove('text-gray-900');
            } else {
                titleElement.classList.remove('line-through', 'text-gray-500');
                titleElement.classList.add('text-gray-900');
            }
        }

        function addSubtaskToDOM(subtask) {
            const container = document.getElementById('subtasks-container');
            const subtaskHtml = `
                <div class="subtask-item flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2" 
                     data-subtask-id="${subtask.id}">
                    <div class="flex items-center flex-1">
                        <input type="checkbox" 
                               onchange="toggleSubtask(${subtask.id})"
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mr-3">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">
                                ${subtask.title}
                            </div>
                            ${subtask.description ? `<div class="text-sm text-gray-600 mt-1">${subtask.description}</div>` : ''}
                        </div>
                    </div>
                    <button onclick="deleteSubtask(${subtask.id})" 
                            class="ml-2 p-1 text-red-600 hover:text-red-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', subtaskHtml);
        }

        function checkIfNoSubtasks() {
            const container = document.getElementById('subtasks-container');
            if (container.children.length === 0) {
                const noSubtasksHtml = `
                    <div id="no-subtasks" class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <p>Chưa có công việc nào. Nhấn "Thêm công việc" để bắt đầu!</p>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', noSubtasksHtml);
            }
        }

        function showProgressBarIfNeeded() {
            const progressContainer = document.querySelector('.mt-2.flex.items-center');
            if (!progressContainer) {
                // Create progress bar if it doesn't exist
                const titleElement = document.querySelector('h3');
                const progressHtml = `
                    <div class="mt-2 flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mr-3">
                            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                                 style="width: 0%" id="progress-bar"></div>
                        </div>
                        <span class="text-sm text-gray-600" id="progress-text">0%</span>
                    </div>
                `;
                titleElement.insertAdjacentHTML('afterend', progressHtml);
            }
        }
    </script>
</x-app-layout>
