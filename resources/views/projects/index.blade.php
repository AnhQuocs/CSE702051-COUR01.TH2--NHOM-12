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
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('projects.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               placeholder="Tìm theo tiêu đề hoặc mô tả..." 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                        <select id="category_id" name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select id="status" name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Tất cả trạng thái</option>
                            <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Chưa bắt đầu</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>Tạm dừng</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Lọc
                        </button>
                    </div>
                </form>
            </div>

            <!-- Projects Grid -->
            @if($projects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                        <div class="bg-white rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                                        <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-600">
                                            {{ $project->title }}
                                        </a>
                                    </h3>
                                    
                                    <!-- Status Badge -->
                                    @php
                                        $statusColors = [
                                            'not_started' => 'bg-gray-100 text-gray-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'on_hold' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                        $statusLabels = [
                                            'not_started' => 'Chưa bắt đầu',
                                            'in_progress' => 'Đang thực hiện',
                                            'completed' => 'Hoàn thành',
                                            'on_hold' => 'Tạm dừng'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$project->status] ?? $project->status }}
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
                                
                                <!-- Actions -->
                                <div class="flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                            Xem
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" class="text-green-600 hover:text-green-800 text-sm">
                                            Sửa
                                        </a>
                                    </div>
                                    
                                    <!-- Priority -->
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
                                    <span class="text-xs font-medium {{ $priorityColors[$project->priority] ?? 'text-gray-600' }}">
                                        {{ $priorityLabels[$project->priority] ?? $project->priority }}
                                    </span>
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
