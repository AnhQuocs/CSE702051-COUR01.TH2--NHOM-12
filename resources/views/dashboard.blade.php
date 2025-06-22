<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <a href="{{ route('projects.create') }}" class="inline-block px-4 py-2 border-2 border-black text-black rounded font-bold bg-white hover:bg-gray-100 transition">
                + Thêm dự án
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Top row with statistics and quick stats widget -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Statistics Cards -->
                <div class="lg:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-500 rounded-lg flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-3 min-w-0 flex-1">
                                <h3 class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Tổng dự án</h3>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-500 rounded-lg flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-3 min-w-0 flex-1">
                                <h3 class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Hoàn thành</h3>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-500 rounded-lg flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 min-w-0 flex-1">
                                <h3 class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Thực hiện</h3>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['in_progress'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-500 rounded-lg flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 min-w-0 flex-1">
                                <h3 class="text-xs sm:text-sm font-semibold text-gray-700 truncate">Quá hạn</h3>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['overdue'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats Widget -->
                @include('components.stats-widget', [
                    'totalProjects' => $stats['total'],
                    'completedProjects' => $stats['completed'],
                    'totalSubtasks' => $stats['total_subtasks'],
                    'completedSubtasks' => $stats['completed_subtasks']
                ])
            </div>

            <!-- Projects List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Theo dõi dự án</h3>
                        <div class="flex items-center space-x-4">
                            <!-- Sort Options -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-700">Sắp xếp theo:</label>
                                <select id="sortSelect" onchange="handleSortChange()" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Mới nhất</option>
                                    <option value="progress" {{ $sortBy == 'progress' ? 'selected' : '' }}>Tiến độ hoàn thành</option>
                                    <option value="deadline" {{ $sortBy == 'deadline' ? 'selected' : '' }}>Thời hạn</option>
                                    <option value="priority" {{ $sortBy == 'priority' ? 'selected' : '' }}>Mức độ ưu tiên</option>
                                </select>
                            </div>
                            <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">Quản lý dự án →</a>
                        </div>
                    </div>

                    @if($projects->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($projects as $project)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer" 
                                     data-project-id="{{ $project->id }}"
                                     onclick="window.location.href='{{ route('projects.show', $project) }}'">
                                    
                                    <!-- Project Header -->
                                    <div class="mb-3">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="text-lg font-semibold text-gray-900 flex-1 mr-2 min-w-0 break-words overflow-hidden">{{ $project->title }}</h4>
                                            @if($project->category)
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full flex-shrink-0" 
                                                      style="background-color: {{ $project->category->color }}20; color: {{ $project->category->color }}">
                                                    {{ $project->category->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Status and Priority Badges -->
                                        <div class="flex items-center space-x-2 mb-2">
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
                                                $priorityColors = [
                                                    'low' => 'bg-green-100 text-green-800',
                                                    'medium' => 'bg-yellow-100 text-yellow-800',
                                                    'high' => 'bg-red-100 text-red-800'
                                                ];
                                                $priorityLabels = [
                                                    'low' => 'Thấp',
                                                    'medium' => 'Trung bình',
                                                    'high' => 'Cao'
                                                ];
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$project->final_status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $statusLabels[$project->final_status] ?? $project->final_status }}
                                            </span>
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $priorityColors[$project->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $priorityLabels[$project->priority] ?? $project->priority }}
                                            </span>
                                        </div>

                                        @if($project->description)
                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($project->description, 120) }}</p>
                                        @endif
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between text-sm mb-1">
                                            <span class="text-gray-600">Tiến độ hoàn thành</span>
                                            <span class="font-medium text-gray-900">{{ $project->progress_percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                                 style="width: {{ $project->progress_percentage }}%"></div>
                                        </div>
                                        @if($project->subtasks->count() > 0)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $project->subtasks->where('is_completed', true)->count() }}/{{ $project->subtasks->count() }} công việc đã hoàn thành
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-500 mt-1">Chưa có công việc nào được tạo</p>
                                        @endif
                                    </div>

                                    <!-- Subtasks List -->
                                    @if($project->subtasks && $project->subtasks->count() > 0)
                                        <div class="mb-4 pt-3 border-t border-gray-100">
                                            <div class="text-xs font-medium text-gray-600 mb-2">Danh sách công việc:</div>
                                            <div class="space-y-2">
                                                @foreach($project->subtasks->take(3) as $subtask)
                                                    <div class="flex items-center space-x-2 text-sm p-2 rounded bg-gray-50" 
                                                         data-project-id="{{ $project->id }}" 
                                                         data-subtask-id="{{ $subtask->id }}">
                                                        <input type="checkbox" 
                                                               {{ $subtask->is_completed ? 'checked' : '' }}
                                                               onchange="toggleSubtaskInDashboard('{{ $subtask->id }}', '{{ $project->id }}')"
                                                               onclick="event.stopPropagation()"
                                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-1">
                                                        <span class="flex-1 min-w-0 text-xs {{ $subtask->is_completed ? 'line-through text-gray-500' : 'text-gray-700' }} break-words line-clamp-2 cursor-pointer hover:text-blue-600 transition-colors"
                                                              onclick="showSubtaskDetail('{{ $subtask->id }}', {{ json_encode($subtask->title) }}, {{ json_encode($subtask->description ?? '') }}, {{ $subtask->is_completed ? 'true' : 'false' }}); event.stopPropagation();">
                                                            {{ $subtask->title }}
                                                            @if(strlen($subtask->title) > 40)
                                                                <span class="text-blue-500 text-xs ml-1">(xem thêm)</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                                
                                                @if($project->subtasks->count() > 3)
                                                    <div class="hidden" id="more-subtasks-{{ $project->id }}">
                                                        @foreach($project->subtasks->skip(3) as $subtask)
                                                            <div class="flex items-center space-x-2 text-sm p-2 rounded bg-gray-50" 
                                                                 data-project-id="{{ $project->id }}" 
                                                                 data-subtask-id="{{ $subtask->id }}">
                                                                <input type="checkbox" 
                                                                       {{ $subtask->is_completed ? 'checked' : '' }}
                                                                       onchange="toggleSubtaskInDashboard('{{ $subtask->id }}', '{{ $project->id }}')"
                                                                       onclick="event.stopPropagation()"
                                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-1">
                                                                <span class="flex-1 min-w-0 text-xs {{ $subtask->is_completed ? 'line-through text-gray-500' : 'text-gray-700' }} break-words line-clamp-2 cursor-pointer hover:text-blue-600 transition-colors"
                                                                      onclick="showSubtaskDetail('{{ $subtask->id }}', {{ json_encode($subtask->title) }}, {{ json_encode($subtask->description ?? '') }}, {{ $subtask->is_completed ? 'true' : 'false' }}); event.stopPropagation();">
                                                                    {{ $subtask->title }}
                                                                    @if(strlen($subtask->title) > 40)
                                                                        <span class="text-blue-500 text-xs ml-1">(xem thêm)</span>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button onclick="toggleMoreSubtasks({{ $project->id }}); event.stopPropagation();" 
                                                            id="toggle-btn-{{ $project->id }}" 
                                                            class="w-full text-xs text-blue-600 hover:text-blue-800 font-medium py-1 bg-blue-50 rounded border border-blue-200 hover:bg-blue-100 transition-colors">
                                                        Hiển thị thêm {{ $project->subtasks->count() - 3 }} công việc
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Project Info Footer -->
                                    <div class="pt-3 border-t border-gray-100">
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            @if($project->end_date)
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Hạn: {{ \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">Chưa có thời hạn</span>
                                            @endif
                                            
                                            <span class="flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $project->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <!-- Nhãn -->
                                        @if($project->tags && $project->tags->count() > 0)
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach($project->tags->take(3) as $tag)
                                                    <span class="inline-flex px-2 py-1 text-xs rounded" 
                                                          style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                        #{{ $tag->name }}
                                                    </span>
                                                @endforeach
                                                @if($project->tags->count() > 3)
                                                    <span class="inline-flex px-2 py-1 text-xs rounded bg-gray-100 text-gray-600">
                                                        +{{ $project->tags->count() - 3 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có dự án nào</h3>
                            <p class="mt-1 text-sm text-gray-500">Hãy bắt đầu bằng cách tạo dự án đầu tiên của bạn.</p>
                            <div class="mt-6">
                                <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tạo dự án mới
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
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
    </div>

    <script>
        console.log('Dashboard script loaded');
        
        // Ensure functions are globally available
        window.handleSortChange = function() {
            console.log('Sort change triggered');
            var sortValue = document.getElementById('sortSelect').value;
            console.log('Selected sort value:', sortValue);
            var currentUrl = new URL(window.location);
            currentUrl.searchParams.set('sort', sortValue);
            console.log('Redirecting to:', currentUrl.toString());
            window.location.href = currentUrl.toString();
        }
        
        // Ensure other functions are globally available
        window.toggleMoreSubtasks = function(projectId) {
            var moreSubtasks = document.getElementById('more-subtasks-' + projectId);
            var toggleBtn = document.getElementById('toggle-btn-' + projectId);
            
            if (moreSubtasks.classList.contains('hidden')) {
                moreSubtasks.classList.remove('hidden');
                toggleBtn.textContent = 'Ẩn bớt';
                toggleBtn.classList.remove('bg-blue-50', 'border-blue-200', 'text-blue-600');
                toggleBtn.classList.add('bg-gray-50', 'border-gray-200', 'text-gray-600');
            } else {
                moreSubtasks.classList.add('hidden');
                var hiddenCount = moreSubtasks.children.length;
                toggleBtn.textContent = 'Hiển thị thêm ' + hiddenCount + ' công việc';
                toggleBtn.classList.remove('bg-gray-50', 'border-gray-200', 'text-gray-600');
                toggleBtn.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-600');
            }
        }
        
        window.toggleSubtaskInDashboard = function(subtaskId, projectId) {
            console.log('Toggling subtask:', subtaskId, 'for project:', projectId);
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var checkbox = document.querySelector('[data-subtask-id="' + subtaskId + '"] input[type="checkbox"]');
            
            var xhr = new XMLHttpRequest();
            xhr.open('PATCH', '/subtasks/' + subtaskId + '/toggle', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', token);
            xhr.setRequestHeader('Accept', 'application/json');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    console.log('Response status:', xhr.status);
                    if (xhr.status === 200) {
                        var data = JSON.parse(xhr.responseText);
                        console.log('Response data:', data);
                        if (data.success) {
                            // Update checkbox
                            checkbox.checked = data.subtask.is_completed;
                            
                            // Update text style
                            var subtaskItem = document.querySelector('[data-subtask-id="' + subtaskId + '"]');
                            var text = subtaskItem.querySelector('span.flex-1');
                            
                            if (data.subtask.is_completed) {
                                text.classList.add('line-through', 'text-gray-500');
                                text.classList.remove('text-gray-700');
                            } else {
                                text.classList.remove('line-through', 'text-gray-500');
                                text.classList.add('text-gray-700');
                            }
                            
                            // Update progress
                            var projectCard = document.querySelector('[data-project-id="' + projectId + '"]');
                            if (projectCard) {
                                var progressBar = projectCard.querySelector('.bg-blue-600');
                                var progressText = projectCard.querySelector('.font-medium.text-gray-900');
                                
                                if (progressBar && progressText) {
                                    progressBar.style.width = data.project.progress_percentage + '%';
                                    progressText.textContent = data.project.progress_percentage + '%';
                                }
                            }
                        }
                    } else {
                        console.error('Error toggling subtask:', xhr.status, xhr.responseText);
                        alert('Có lỗi xảy ra khi cập nhật trạng thái công việc!');
                        checkbox.checked = !checkbox.checked;
                    }
                }
            };
            
            xhr.send();
        }

        // Show subtask detail modal
        window.showSubtaskDetail = function(subtaskId, title, description, isCompleted) {
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
        window.closeSubtaskDetail = function() {
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
                }
            }
        });
        
        // Debug: Check if functions are available
        console.log('Functions available:', {
            handleSortChange: typeof window.handleSortChange,
            toggleMoreSubtasks: typeof window.toggleMoreSubtasks,
            toggleSubtaskInDashboard: typeof window.toggleSubtaskInDashboard,
            showSubtaskDetail: typeof window.showSubtaskDetail,
            closeSubtaskDetail: typeof window.closeSubtaskDetail
        });
    </script>
</x-app-layout>
