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
                                <span class="text-sm font-medium text-gray-500">Trạng thái:</span>
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
                                <span class="ml-2 px-3 py-1 text-sm font-medium rounded-full {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$project->status] ?? $project->status }}
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
                    </div>
                @endif
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
            </div>
        </div>
    </div>
</x-app-layout>
