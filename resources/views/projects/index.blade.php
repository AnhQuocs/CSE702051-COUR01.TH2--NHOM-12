<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Danh sách dự án') }}
            </h2>
            <a href="{{ route('projects.create') }}" class="inline-block px-4 py-2 border-2 border-black text-black rounded font-bold bg-white hover:bg-gray-100 transition">
                + Thêm dự án
            </a>
        </div>
    </x-slot>    <!-- Add CSS for tag filter and bulk actions -->
    <link rel="stylesheet" href="{{ asset('css/tag-filter.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bulk-actions.css') }}"><div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>            @endif              <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-6 mb-6 filters-container">
                <form method="GET" action="{{ route('projects.index') }}" id="filter-form">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 filters-grid">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               placeholder="Tìm theo tiêu đề hoặc mô tả..." 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                            <select id="category_id" name="category_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div><div class="tag-filter-container">
                        <label for="tag_id" class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="tag-icon">#</span>Tag
                        </label>                        <select id="tag_id" name="tag_id" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Tất cả tag</option>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                                    #{{ $tag->name }}{{ $tag->projects_count > 0 ? ' (' . $tag->projects_count . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select id="status" name="status" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Tất cả trạng thái</option>
                                <option value="not_planned" {{ request('status') == 'not_planned' ? 'selected' : '' }}>Chưa lên kế hoạch</option>
                                <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Chưa bắt đầu</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Quá hạn</option>
                            </select>
                        </div>
                    
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp</label>
                        <select id="sort_by" name="sort_by" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                            <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Tên dự án</option>
                            <option value="end_date" {{ request('sort_by') == 'end_date' ? 'selected' : '' }}>Ngày kết thúc</option>                            <option value="priority" {{ request('sort_by') == 'priority' ? 'selected' : '' }}>Mức độ ưu tiên</option>                        </select>
                    </div>
                </div>
                
                <!-- Clear filter link only -->
                @if(request()->hasAny(['search', 'category_id', 'tag_id', 'status', 'sort_by']))
                    <div class="mt-4 text-right">
                        <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center justify-end">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Xóa bộ lọc
                        </a>
                    </div>
                @endif
                </form>
            </div>            <!-- Results summary and bulk actions -->
            @if($projects->count() > 0)
                <div class="flex items-center justify-between mb-6">
                    <div class="text-sm text-gray-600">
                        Hiển thị {{ $projects->count() }} dự án
                        @if(request()->hasAny(['search', 'category_id', 'tag_id', 'status']))
                            @if(request('search'))
                                cho từ khóa "<strong>{{ request('search') }}</strong>"
                            @endif
                            @if(request('category_id'))
                                @php $selectedCategory = $categories->find(request('category_id')) @endphp
                                trong danh mục "<strong>{{ $selectedCategory->name ?? 'N/A' }}</strong>"
                            @endif
                            @if(request('tag_id'))
                                @php $selectedTag = $tags->find(request('tag_id')) @endphp
                                với tag "<strong>#{{ $selectedTag->name ?? 'N/A' }}</strong>"
                            @endif
                            @if(request('status'))
                                @php
                                    $statusLabels = [
                                        'not_planned' => 'Chưa lên kế hoạch',
                                        'not_started' => 'Chưa bắt đầu', 
                                        'in_progress' => 'Đang thực hiện',
                                        'completed' => 'Hoàn thành',
                                        'overdue' => 'Quá hạn'
                                    ];
                                @endphp
                                với trạng thái "<strong>{{ $statusLabels[request('status')] ?? request('status') }}</strong>"
                            @endif
                        @endif
                    </div>
                    
                    <!-- Select All Checkbox -->
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center text-sm">
                            <input type="checkbox" id="select-all" class="mr-2 rounded border-gray-300 focus:ring-blue-500">
                            Chọn tất cả
                        </label>
                    </div>
                </div>

                <!-- Bulk Actions Bar (Hidden by default) -->
                <div id="bulk-actions-bar" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span id="selected-count" class="text-sm font-medium text-blue-900">0 dự án được chọn</span>
                            
                            <div class="flex items-center space-x-2">
                                <select id="bulk-action" class="border border-blue-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Chọn hành động...</option>
                                    <option value="delete">Xóa các dự án</option>
                                    <option value="status_not_started">Đặt trạng thái: Chưa bắt đầu</option>
                                    <option value="status_in_progress">Đặt trạng thái: Đang thực hiện</option>
                                    <option value="status_completed">Đặt trạng thái: Hoàn thành</option>
                                    <option value="export">Xuất dữ liệu</option>
                                </select>
                                
                                <button type="button" id="apply-bulk-action" class="px-4 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                                    Áp dụng
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" id="clear-selection" class="text-blue-600 hover:text-blue-800 text-sm">
                            Bỏ chọn tất cả
                        </button>
                    </div>
                </div>
            @endif

            <!-- Projects Grid -->
            @if($projects->count() > 0)                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 projects-grid" id="projects-grid">
                    @foreach($projects as $project)
                        <div class="bg-white rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow project-card relative">
                            <!-- Project Checkbox -->
                            <div class="absolute top-3 left-3 z-10">
                                <input type="checkbox" 
                                       class="project-checkbox rounded border-gray-300 focus:ring-blue-500" 
                                       data-project-id="{{ $project->id }}"
                                       value="{{ $project->id }}">
                            </div>
                            
                            <div class="p-6 pl-10">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900 line-clamp-2 flex-1 min-w-0 break-words overflow-hidden mr-3">
                                        <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-600">
                                            {{ $project->title }}
                                        </a>
                                    </h3>                                    
                                    <!-- Status Badge -->@php
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
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$project->final_status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$project->final_status] ?? $project->final_status }}
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                    {{ $project->description }}
                                </p>
                                
                                <!-- Category & Tags -->
                                <div class="mb-4">
                                    @if($project->category)
                                        <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded mr-2 mb-1">
                                            {{ $project->category->name }}
                                        </span>
                                    @endif
                                    
                                    @foreach($project->tags as $tag)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2 mb-1">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                                  <!-- Dates -->
                                <div class="text-xs text-gray-500 mb-4">
                                    @if($project->start_date)
                                        <div>Bắt đầu: {{ \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') }}</div>
                                    @endif
                                    @if($project->end_date)
                                        <div>Kết thúc: {{ \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') }}</div>
                                    @endif
                                </div>

                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between text-sm mb-2">
                                        <span class="text-gray-600">Tiến độ hoàn thành</span>
                                        <span class="font-medium text-gray-900">{{ $project->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $project->progress_percentage }}%"></div>
                                    </div>
                                    @if($project->subtasks->count() > 0)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $project->subtasks->where('is_completed', true)->count() }}/{{ $project->subtasks->count() }} công việc hoàn thành
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-500 mt-1">Chưa có công việc nào</p>
                                    @endif
                                </div>

                                <!-- Priority -->
                                <div class="mb-4">
                                    @php
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
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $priorityColors[$project->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                        Độ ưu tiên: {{ $priorityLabels[$project->priority] ?? $project->priority }}
                                    </span>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center justify-between gap-2 pt-2 border-t border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('projects.show', $project) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Xem
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Sửa
                                        </a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline" 
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $projects->withQueryString()->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Không có dự án nào</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'category_id', 'status']))
                            Không tìm thấy dự án nào phù hợp với bộ lọc của bạn.
                        @else
                            Bắt đầu bằng cách tạo dự án đầu tiên của bạn.
                        @endif
                    </p>
                    <div class="mt-6">
                        @if(request()->hasAny(['search', 'category_id', 'status']))
                            <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Xóa bộ lọc
                            </a>
                        @else
                            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                + Tạo dự án mới
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<!-- Custom styles for real-time filtering -->
        <style>
            .filter-loading {
                position: relative;
            }
            
            .filter-loading::after {
                content: '';
                position: absolute;
                right: 8px;
                top: 50%;
                transform: translateY(-50%);
                width: 16px;
                height: 16px;
                border: 2px solid #f3f3f3;
                border-top: 2px solid #3498db;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: translateY(-50%) rotate(0deg); }
                100% { transform: translateY(-50%) rotate(360deg); }
            }
            
            .filter-active {
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
                border-color: #3b82f6 !important;
            }
            
            .projects-grid {
                transition: opacity 0.3s ease;
            }
            
            .projects-loading {
                opacity: 0.6;
                pointer-events: none;
            }
              /* Improved hover effects */
            .project-card {
                transition: all 0.3s ease;
            }
            
            .project-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }
            
            /* Tag selector styling */
            #tag_id option {
                font-family: ui-monospace, 'Cascadia Code', 'Source Code Pro', Menlo, Monaco, Consolas, 'Roboto Mono', monospace;
                font-weight: 500;
            }
        </style>    <script>
        // Real-time search and filter functionality        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const categorySelect = document.getElementById('category_id');
            const tagSelect = document.getElementById('tag_id');
            const statusSelect = document.getElementById('status');
            const sortSelect = document.getElementById('sort_by');
            const projectsGrid = document.getElementById('projects-grid');
            const filterForm = document.getElementById('filter-form');
            
            let searchTimeout;            // Function to submit form with filters
            function updateFilters() {
                console.log('updateFilters called');
                
                // Build URL with current form values
                const url = new URL(window.location.origin + '{{ route("projects.index") }}');
                
                if (searchInput.value.trim()) {
                    url.searchParams.set('search', searchInput.value.trim());
                }
                if (categorySelect.value) {
                    url.searchParams.set('category_id', categorySelect.value);
                }
                if (tagSelect.value) {
                    url.searchParams.set('tag_id', tagSelect.value);
                }
                if (statusSelect.value) {
                    url.searchParams.set('status', statusSelect.value);
                }
                if (sortSelect.value && sortSelect.value !== 'created_at') {
                    url.searchParams.set('sort_by', sortSelect.value);
                }
                
                console.log('Redirecting to:', url.toString());
                window.location.href = url.toString();
            }
            
            // Real-time search with debounce (delay typing)
            searchInput.addEventListener('input', function() {
                // Add loading indicator to search input
                searchInput.classList.add('filter-loading');
                
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    updateFilters();
                }, 800); // Wait 800ms after user stops typing for better UX
            });            // Immediate filter on select change
            categorySelect.addEventListener('change', function() {
                console.log('Category changed:', this.value);
                this.classList.add('filter-loading');
                updateFilters();
            });
            
            tagSelect.addEventListener('change', function() {
                console.log('Tag changed:', this.value);
                this.classList.add('filter-loading');
                updateFilters();
            });
            
            statusSelect.addEventListener('change', function() {
                console.log('Status changed:', this.value);
                this.classList.add('filter-loading');
                updateFilters();
            });
            
            sortSelect.addEventListener('change', function() {
                this.classList.add('filter-loading');
                updateFilters();
            });
            
            // Add visual feedback for active filters
            function updateFilterStyles() {                const filters = [
                    { element: searchInput, hasValue: searchInput.value.trim() !== '' },
                    { element: categorySelect, hasValue: categorySelect.value !== '' },
                    { element: tagSelect, hasValue: tagSelect.value !== '' },
                    { element: statusSelect, hasValue: statusSelect.value !== '' },
                    { element: sortSelect, hasValue: sortSelect.value !== 'created_at' }
                ];
                
                filters.forEach(filter => {
                    const label = filter.element.parentElement.querySelector('label');
                    
                    if (filter.hasValue) {
                        filter.element.classList.add('filter-active');
                        if (label) {
                            label.classList.add('text-blue-600', 'font-semibold');
                        }
                    } else {
                        filter.element.classList.remove('filter-active');
                        if (label) {
                            label.classList.remove('text-blue-600', 'font-semibold');
                        }
                    }
                });
            }
            
            // Update styles on page load
            updateFilterStyles();
              // Update styles when filters change
            [searchInput, categorySelect, tagSelect, statusSelect, sortSelect].forEach(element => {
                element.addEventListener('input', updateFilterStyles);
                element.addEventListener('change', updateFilterStyles);
            });
            
            // Clear button functionality
            const clearFiltersBtn = document.querySelector('a[href="{{ route("projects.index") }}"]');
            if (clearFiltersBtn && clearFiltersBtn.textContent.includes('Xóa bộ lọc')) {
                clearFiltersBtn.addEventListener('click', function() {
                    if (projectsGrid) {
                        projectsGrid.classList.add('projects-loading');
                    }
                });
            }
              // Improve form submission UX
            const hasActiveFilters = searchInput.value || categorySelect.value || tagSelect.value || statusSelect.value || (sortSelect.value !== 'created_at');
            if (hasActiveFilters) {
                document.body.classList.add('has-active-filters');
            }        });
        
        // Prevent multiple rapid submissions
        let isSubmitting = false;
        window.addEventListener('beforeunload', function() {
            if (isSubmitting) return;
            isSubmitting = true;
        });

        // Bulk Actions JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const projectCheckboxes = document.querySelectorAll('.project-checkbox');
            const bulkActionsBar = document.getElementById('bulk-actions-bar');
            const selectedCountSpan = document.getElementById('selected-count');
            const bulkActionSelect = document.getElementById('bulk-action');
            const applyBulkActionButton = document.getElementById('apply-bulk-action');
            const clearSelectionButton = document.getElementById('clear-selection');

            // Update bulk actions bar visibility and selected count
            function updateBulkActionsBar() {
                const selectedCheckboxes = document.querySelectorAll('.project-checkbox:checked');
                const selectedCount = selectedCheckboxes.length;

                if (selectedCount > 0) {
                    bulkActionsBar.classList.remove('hidden');
                    selectedCountSpan.textContent = `${selectedCount} dự án được chọn`;
                    applyBulkActionButton.disabled = !bulkActionSelect.value;
                } else {
                    bulkActionsBar.classList.add('hidden');
                    bulkActionSelect.value = '';
                    applyBulkActionButton.disabled = true;
                }

                // Update select all checkbox state
                if (selectedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (selectedCount === projectCheckboxes.length) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }

            // Handle select all checkbox
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                projectCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateBulkActionsBar();
            });

            // Handle individual project checkboxes
            projectCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActionsBar);
            });

            // Handle bulk action selection change
            bulkActionSelect.addEventListener('change', function() {
                applyBulkActionButton.disabled = !this.value;
            });

            // Handle clear selection
            clearSelectionButton.addEventListener('click', function() {
                projectCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateBulkActionsBar();
            });

            // Handle apply bulk action
            applyBulkActionButton.addEventListener('click', function() {
                const selectedProjectIds = Array.from(document.querySelectorAll('.project-checkbox:checked'))
                    .map(checkbox => checkbox.value);
                const action = bulkActionSelect.value;

                if (selectedProjectIds.length === 0 || !action) {
                    return;
                }

                // Show confirmation for destructive actions
                if (action === 'delete') {
                    if (!confirm(`Bạn có chắc chắn muốn xóa ${selectedProjectIds.length} dự án đã chọn? Hành động này không thể hoàn tác.`)) {
                        return;
                    }
                }

                // Disable button to prevent double-click
                this.disabled = true;
                this.textContent = 'Đang xử lý...';

                // Execute the action
                executeBulkAction(action, selectedProjectIds);
            });

            // Execute bulk action function
            function executeBulkAction(action, projectIds) {
                let url, data;

                if (action === 'delete') {
                    url = '{{ route("projects.bulk-delete") }}';
                    data = { project_ids: projectIds };
                } else if (action.startsWith('status_')) {
                    url = '{{ route("projects.bulk-status") }}';
                    const status = action.replace('status_', '');
                    data = { project_ids: projectIds, status: status };
                } else if (action === 'export') {
                    url = '{{ route("projects.bulk-export") }}';
                    data = { project_ids: projectIds, format: 'csv' };
                }

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (action === 'export' && response.ok) {
                        // Handle file download for export
                        return response.blob().then(blob => {
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `projects_export_${new Date().toISOString().slice(0,10)}.csv`;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);
                            
                            showToast('Xuất dữ liệu thành công!', 'success');
                            resetBulkActions();
                        });
                    } else {
                        return response.json();
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        showToast(data.message, 'success');
                        
                        // Reload page for delete and status change actions
                        if (action === 'delete' || action.startsWith('status_')) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            resetBulkActions();
                        }
                    } else if (data && !data.success) {
                        showToast(data.error || 'Có lỗi xảy ra!', 'error');
                        resetBulkActions();
                    }
                })
                .catch(error => {
                    console.error('Bulk action error:', error);
                    showToast('Có lỗi xảy ra khi thực hiện thao tác!', 'error');
                    resetBulkActions();
                });
            }

            // Reset bulk actions UI
            function resetBulkActions() {
                applyBulkActionButton.disabled = false;
                applyBulkActionButton.textContent = 'Áp dụng';
                bulkActionSelect.value = '';
                
                // Clear all selections
                projectCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateBulkActionsBar();
            }

            // Show toast notification
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white transition-all duration-300 ${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                }`;
                toast.textContent = message;
                
                document.body.appendChild(toast);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                }, 3000);
            }
        });
    </script>
