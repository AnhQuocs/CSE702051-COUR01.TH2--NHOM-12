<x-app-layout>    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight min-w-0 flex-1 mr-4 break-words overflow-hidden">
                {{ $project->title }}
            </h2>
            <div class="flex space-x-2 flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="inline-block px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition">
                    ← Quay lại Dashboard
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
                </div>                <!-- Subtasks List -->
                <div id="subtasks-container">
                    @if($project->subtasks->count() > 0)
                        <div id="sortable-subtasks" class="space-y-3">
                            @foreach($project->subtasks->sortBy('order') as $subtask)
                                <div class="subtask-item flex items-center justify-between p-4 bg-gray-50 rounded-lg border transition-all duration-200 hover:shadow-md" 
                                     data-subtask-id="{{ $subtask->id }}">
                                    
                                    <!-- Drag Handle -->
                                    <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                        </svg>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3 flex-1">                                        <input type="checkbox" 
                                               {{ $subtask->is_completed ? 'checked' : '' }}
                                               onchange="toggleSubtask('{{ $subtask->id }}')"
                                               class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">

                                        <div class="flex-1 min-w-0">                                            <div class="font-medium {{ $subtask->is_completed ? 'line-through text-gray-500' : 'text-gray-900' }} break-words">
                                                <span class="line-clamp-2 cursor-pointer hover:text-blue-600 transition-colors" 
                                                      onclick="showSubtaskDetail('{{ $subtask->id }}', {{ json_encode($subtask->title) }}, {{ json_encode($subtask->description ?? '') }}, {{ $subtask->is_completed ? 'true' : 'false' }})">
                                                    {{ $subtask->title }}
                                                    @if(strlen($subtask->title) > 50)
                                                        <span class="text-blue-500 text-xs ml-1">(xem thêm)</span>
                                                    @endif
                                                </span>
                                            </div>
                                            @if($subtask->description)
                                                <div class="text-sm text-gray-500 mt-1 {{ $subtask->is_completed ? 'line-through' : '' }} break-words">
                                                    <span class="line-clamp-2 cursor-pointer hover:text-blue-400 transition-colors" 
                                                          onclick="showSubtaskDetail('{{ $subtask->id }}', {{ json_encode($subtask->title) }}, {{ json_encode($subtask->description) }}, {{ $subtask->is_completed ? 'true' : 'false' }})">
                                                        {{ $subtask->description }}
                                                        @if(strlen($subtask->description) > 100)
                                                            <span class="text-blue-400 text-xs ml-1">(xem thêm)</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>                                    <button onclick="deleteSubtask('{{ $subtask->id }}'); event.stopPropagation();" 
                                            class="ml-3 text-red-600 hover:text-red-800 p-1 flex-shrink-0"
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
                                    <span class="text-sm font-medium text-gray-500">Tags:</span>
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
                        <p class="mt-2 text-gray-700 whitespace-pre-line break-words overflow-hidden">{{ $project->description }}</p>
                    </div>
                @endif            </div>
        </div>
    </div>

    <!-- Subtask Detail Modal -->
    <div id="subtask-detail-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Chi tiết công việc</h3>
                    <button onclick="closeSubtaskDetail()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="mt-4 space-y-4">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tiêu đề:</label>
                        <p id="modal-subtask-title" class="text-gray-900 font-medium break-words whitespace-pre-wrap"></p>
                    </div>
                    
                    <!-- Description -->
                    <div id="modal-description-section" class="hidden">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Mô tả:</label>
                        <p id="modal-subtask-description" class="text-gray-700 break-words whitespace-pre-wrap"></p>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Trạng thái:</label>
                        <span id="modal-subtask-status" class="inline-flex px-3 py-1 text-sm font-medium rounded-full"></span>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end pt-4 border-t border-gray-200 mt-6">
                    <button onclick="closeSubtaskDetail()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>    <script>
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
        }        // Add new subtask
        document.getElementById('subtask-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
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
            });        });            // Toggle subtask completion with auto-save
        function toggleSubtask(subtaskId) {
            console.log('🔄 Toggle subtask called:', subtaskId);
            
            const subtaskItem = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
            const checkbox = subtaskItem?.querySelector('input[type="checkbox"]');
            const titleDiv = subtaskItem?.querySelector('.font-medium');
            const descDiv = subtaskItem?.querySelector('.text-sm.text-gray-500');
            
            console.log('📋 Elements found:', {
                subtaskItem: !!subtaskItem,
                checkbox: !!checkbox,
                titleDiv: !!titleDiv,
                descDiv: !!descDiv,
                originalState: checkbox?.checked
            });
            
            if (!subtaskItem || !checkbox) {
                console.error('❌ Required elements not found!');
                return;
            }
              // Store original state for revert if needed
            const originalState = checkbox.checked;
            console.log('💾 Original checkbox state:', originalState);
            
            // Get CSRF token
            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenElement) {
                console.error('❌ CSRF token not found!');
                alert('Lỗi bảo mật: Không tìm thấy CSRF token. Vui lòng tải lại trang.');
                return;
            }
            
            const csrfToken = csrfTokenElement.getAttribute('content');
            console.log('🔐 CSRF token found:', csrfToken ? 'Yes' : 'No');
            
            // Use real route with proper CSRF
            const toggleUrl = `/subtasks/${subtaskId}/toggle`;
            console.log('🌐 Calling URL:', toggleUrl);
              fetch(toggleUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('📡 Response status:', response.status, response.statusText);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('❌ Response not OK:', text);
                        throw new Error(`HTTP ${response.status}: ${response.statusText} - ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Server response:', data);
                if (data.success) {
                    // Update checkbox based on server response
                    const newState = data.subtask.is_completed;
                    checkbox.checked = newState;
                    
                    console.log('🔄 Updating UI state:', {
                        oldState: originalState,
                        newState: newState,
                        checkboxState: checkbox.checked
                    });
                    
                    // Update text styling
                    if (newState) {
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
                    
                    console.log('🎉 Subtask toggle completed successfully');
                } else {
                    console.error('❌ Server returned success=false:', data);
                    // Revert checkbox state
                    checkbox.checked = originalState;
                    alert('Có lỗi xảy ra khi cập nhật trạng thái công việc!');
                }
            })
            .catch(error => {
                console.error('💥 Error toggling subtask:', error);
                // Revert checkbox state
                checkbox.checked = originalState;
                alert('Có lỗi xảy ra: ' + error.message);
            });
        }// Update progress bar and status
        function updateProgressAndStatus(projectData) {
            console.log('Updating progress and status with data:', projectData);
            
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
                const completedCount = projectData.completed_subtasks_count || projectData.completed_subtasks || 0;
                const totalCount = projectData.subtasks_count || projectData.total_subtasks || 0;
                progressStats.textContent = `${completedCount}/${totalCount} công việc đã hoàn thành`;
            }
            
            // Update status badge
            if (projectStatus) {
                projectStatus.className = `inline-flex px-4 py-2 text-sm font-medium rounded-full ${statusColors[projectData.final_status] || 'bg-gray-100 text-gray-800'}`;
                projectStatus.textContent = statusLabels[projectData.final_status] || projectData.final_status;
            }
        }        // Delete subtask
        function deleteSubtask(subtaskId) {
            if (!confirm('Bạn có chắc chắn muốn xóa công việc này?')) {
                return;
            }
            
            console.log('Deleting subtask:', subtaskId);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/subtasks/${subtaskId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('Delete response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Delete response data:', data);
                if (data.success) {
                    // Remove the subtask element from DOM
                    const subtaskElement = document.querySelector(`[data-subtask-id="${subtaskId}"]`);
                    if (subtaskElement) {
                        subtaskElement.remove();
                    }
                    
                    // Update progress and status if project data is returned
                    if (data.progress !== undefined) {
                        updateProgressAndStatus({
                            progress_percentage: data.progress,
                            final_status: data.status,
                            completed_subtasks: data.completed_subtasks || 0,
                            total_subtasks: data.total_subtasks || 0
                        });
                    }
                    
                    // If no more subtasks, reload page to show empty state
                    const remainingSubtasks = document.querySelectorAll('.subtask-item');
                    if (remainingSubtasks.length === 0) {
                        location.reload();
                    }
                    
                    console.log('Subtask deleted successfully');
                } else {
                    alert('Có lỗi xảy ra khi xóa công việc!');
                }
            })
            .catch(error => {
                console.error('Error deleting subtask:', error);
                alert('Có lỗi xảy ra khi xóa công việc: ' + error.message);
            });
        }

        // Show subtask detail modal
        function showSubtaskDetail(subtaskId, title, description, isCompleted) {
            const modal = document.getElementById('subtask-detail-modal');
            const titleElement = document.getElementById('modal-subtask-title');
            const descriptionElement = document.getElementById('modal-subtask-description');
            const descriptionSection = document.getElementById('modal-description-section');
            const statusElement = document.getElementById('modal-subtask-status');
            
            // Set title
            titleElement.textContent = title;
            
            // Set description
            if (description && description.trim() !== '') {
                descriptionElement.textContent = description;
                descriptionSection.classList.remove('hidden');
            } else {
                descriptionSection.classList.add('hidden');
            }
            
            // Set status
            if (isCompleted) {
                statusElement.textContent = 'Đã hoàn thành';
                statusElement.className = 'inline-flex px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800';
            } else {
                statusElement.textContent = 'Chưa hoàn thành';
                statusElement.className = 'inline-flex px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800';
            }
            
            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        // Close subtask detail modal
        function closeSubtaskDetail() {
            const modal = document.getElementById('subtask-detail-modal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        document.getElementById('subtask-detail-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSubtaskDetail();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('subtask-detail-modal');
                if (!modal.classList.contains('hidden')) {
                    closeSubtaskDetail();
                }            }
        });
        
        // Initialize drag & drop functionality
        initDragAndDrop();
    </script>

    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <!-- Drag & Drop Specific Script -->
    <script>
        function initDragAndDrop() {
            const sortableList = document.getElementById('sortable-subtasks');
            if (!sortableList) return;

            new Sortable(sortableList, {
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                handle: '.drag-handle',
                onStart: function (evt) {
                    evt.item.classList.add('dragging');
                },
                onEnd: function (evt) {
                    evt.item.classList.remove('dragging');
                    
                    // Get new order of subtask IDs
                    const subtaskIds = Array.from(sortableList.children).map(item => 
                        item.getAttribute('data-subtask-id')
                    );
                    
                    // Send to backend
                    updateSubtasksOrder(subtaskIds);
                }
            });
        }

        function updateSubtasksOrder(subtaskIds) {
            const projectId = {{ $project->id }};
            
            fetch(`/projects/${projectId}/subtasks/order`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    subtask_ids: subtaskIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Subtasks order updated successfully');
                    // Show success toast (optional)
                    showToast('Đã cập nhật thứ tự công việc', 'success');
                } else {
                    console.error('Failed to update order:', data.error);
                    showToast('Lỗi khi cập nhật thứ tự', 'error');
                    // Optionally reload to restore original order
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Lỗi kết nối', 'error');
                // Optionally reload to restore original order
                location.reload();
            });
        }

        function showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-md shadow-lg text-white transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>

    <!-- Drag & Drop CSS -->
    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: #f3f4f6;
        }
        
        .sortable-chosen {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .sortable-drag {
            transform: rotate(5deg);
        }
        
        .dragging {
            opacity: 0.8;
            transform: scale(1.05);
            z-index: 1000;
        }
        
        .drag-handle:hover {
            transform: scale(1.1);
        }
        
        .subtask-item {
            transition: all 0.2s ease;
        }
        
        .subtask-item:hover .drag-handle {
            color: #6b7280;
        }
    </style>
</x-app-layout>
