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
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-500 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-700">Tổng số dự án</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-500 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-700">Đã hoàn thành</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-500 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-700">Đang thực hiện</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['in_progress'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-500 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-700">Quá hạn</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['overdue'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Dự án gần đây</h3>
                        <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">Xem tất cả →</a>
                    </div>

                    @if($projects->count() > 0)
                        <div class="space-y-4">
                            @foreach($projects as $project)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $project->title }}</h4>
                                                
                                                @if($project->category)
                                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" 
                                                          style="background-color: {{ $project->category->color }}20; color: {{ $project->category->color }}">
                                                        {{ $project->category->name }}
                                                    </span>
                                                @endif

                                                <!-- Status Badge -->
                                                @php
                                                    $statusColors = [
                                                        'not_started' => 'bg-gray-100 text-gray-800',
                                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                                        'completed' => 'bg-green-100 text-green-800',
                                                        'overdue' => 'bg-red-100 text-red-800'
                                                    ];
                                                    $statusLabels = [
                                                        'not_started' => 'Chưa bắt đầu',
                                                        'in_progress' => 'Đang thực hiện',
                                                        'completed' => 'Hoàn thành',
                                                        'overdue' => 'Quá hạn'
                                                    ];
                                                @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$project->final_status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $statusLabels[$project->final_status] ?? $project->final_status }}
                                                </span>

                                                <!-- Priority Badge -->
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
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $priorityColors[$project->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $priorityLabels[$project->priority] ?? $project->priority }}
                                                </span>
                                            </div>

                                            @if($project->description)
                                                <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ Str::limit($project->description, 150) }}</p>
                                            @endif

                                            <!-- Progress Bar -->
                                            <div class="mb-3">
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

                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                @if($project->end_date)
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Hạn: {{ \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                                
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $project->created_at->diffForHumans() }}
                                                </span>
                                            </div>

                                            <!-- Tags -->
                                            @if($project->tags && $project->tags->count() > 0)
                                                <div class="flex flex-wrap gap-1 mt-2">
                                                    @foreach($project->tags as $tag)
                                                        <span class="inline-flex px-2 py-1 text-xs rounded" 
                                                              style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                            #{{ $tag->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 ml-4">
                                            <!-- View Button -->
                                            <a href="{{ route('projects.show', $project) }}" 
                                               class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors"
                                               title="Xem chi tiết dự án">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Xem
                                            </a>
                                            
                                            <!-- Edit Button -->
                                            <a href="{{ route('projects.edit', $project) }}" 
                                               class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 transition-colors"
                                               title="Chỉnh sửa dự án">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Sửa
                                            </a>
                                            
                                            <!-- Delete Button -->
                                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa dự án này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition-colors"
                                                        title="Xóa dự án">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Xóa
                                                </button>
                                            </form>
                                        </div>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
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
</x-app-layout>
