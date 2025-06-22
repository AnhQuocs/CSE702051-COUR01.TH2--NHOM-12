<x-app-layout>    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Báo cáo chi tiết
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('stats.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                    ← Quay lại thống kê
                </a>
                <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                    🖨️ In báo cáo
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Report Header -->
            <div class="bg-white rounded-lg shadow-md p-6 print:shadow-none">
                <div class="text-center border-b pb-4 mb-4">
                    <h1 class="text-2xl font-bold text-gray-900">Báo cáo thống kê hoạt động người dùng</h1>
                    <p class="text-gray-600 mt-2">{{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
                    <p class="text-sm text-gray-500">Báo cáo được tạo vào: {{ now()->format('d/m/Y H:i:s') }}</p>
                </div>
                
                <!-- Executive Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $totalProjects }}</div>
                        <div class="text-sm text-gray-600">Tổng số dự án</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $completedProjects }}</div>
                        <div class="text-sm text-gray-600">Dự án hoàn thành</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $totalSubtasks }}</div>
                        <div class="text-sm text-gray-600">Tổng công việc</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600">{{ $completedSubtasks }}</div>
                        <div class="text-sm text-gray-600">Công việc hoàn thành</div>
                    </div>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Projects by Status -->
                <div class="bg-white rounded-lg shadow-md p-6 print:shadow-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Phân tích trạng thái dự án</h3>
                    <div class="space-y-3">
                        @php
                            $statusLabels = [
                                'not_planned' => 'Chưa lên kế hoạch',
                                'not_started' => 'Chưa bắt đầu',
                                'in_progress' => 'Đang thực hiện',
                                'completed' => 'Hoàn thành',
                                'overdue' => 'Quá hạn'
                            ];
                        @endphp
                        
                        @foreach($projectsByStatus as $status => $count)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">{{ $statusLabels[$status] ?? $status }}</span>
                                <div class="flex items-center">
                                    <span class="font-medium mr-2">{{ $count }}</span>
                                    <span class="text-sm text-gray-500">
                                        ({{ $totalProjects > 0 ? round(($count / $totalProjects) * 100, 1) : 0 }}%)
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Projects by Priority -->
                <div class="bg-white rounded-lg shadow-md p-6 print:shadow-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Phân tích mức độ ưu tiên</h3>
                    <div class="space-y-3">
                        @php
                            $priorityLabels = [
                                'low' => 'Thấp',
                                'medium' => 'Trung bình',
                                'high' => 'Cao'
                            ];
                        @endphp
                        
                        @foreach($priorityLabels as $priority => $label)
                            @php $count = $projectsByPriority[$priority] ?? 0 @endphp
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">{{ $label }}</span>
                                <div class="flex items-center">
                                    <span class="font-medium mr-2">{{ $count }}</span>
                                    <span class="text-sm text-gray-500">
                                        ({{ $totalProjects > 0 ? round(($count / $totalProjects) * 100, 1) : 0 }}%)
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white rounded-lg shadow-md p-6 print:shadow-none">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Chỉ số hiệu suất</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">
                            @if($totalProjects > 0)
                                {{ round(($completedProjects / $totalProjects) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">Tỷ lệ hoàn thành dự án</div>
                    </div>
                    
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">
                            @if($totalSubtasks > 0)
                                {{ round(($completedSubtasks / $totalSubtasks) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">Tỷ lệ hoàn thành công việc</div>
                    </div>
                    
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">
                            @if($totalProjects > 0)
                                {{ round($totalSubtasks / $totalProjects, 1) }}
                            @else
                                0
                            @endif
                        </div>
                        <div class="text-sm text-gray-600">Công việc trung bình/dự án</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-md p-6 print:shadow-none">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hoạt động gần đây (30 ngày)</h3>
                
                @if($recentProjects->count() > 0)
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-2">Dự án mới được tạo:</h4>
                        <div class="space-y-2">
                            @foreach($recentProjects as $project)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <div>
                                        <span class="font-medium">{{ $project->title }}</span>
                                        @if($project->description)
                                            <p class="text-sm text-gray-600">{{ Str::limit($project->description, 100) }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">{{ $project->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $project->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($recentSubtasks->count() > 0)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Công việc hoàn thành gần đây:</h4>
                        <div class="space-y-2">
                            @foreach($recentSubtasks->take(10) as $subtask)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <div>
                                        <span class="font-medium">{{ $subtask->title }}</span>
                                        <p class="text-sm text-gray-600">{{ $subtask->project->title }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">{{ $subtask->updated_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $subtask->updated_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($recentProjects->count() == 0 && $recentSubtasks->count() == 0)
                    <p class="text-gray-500 text-center py-8">Không có hoạt động nào trong 30 ngày qua.</p>
                @endif
            </div>

            <!-- Recommendations -->
            <div class="bg-white rounded-lg shadow-md p-6 print:shadow-none">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Khuyến nghị cải thiện</h3>
                <div class="space-y-3">
                    @php
                        $completionRate = $totalProjects > 0 ? ($completedProjects / $totalProjects) * 100 : 0;
                        $taskCompletionRate = $totalSubtasks > 0 ? ($completedSubtasks / $totalSubtasks) * 100 : 0;
                        $overdueCount = $projectsByStatus['overdue'] ?? 0;
                    @endphp
                    
                    @if($completionRate < 50)
                        <div class="p-3 bg-red-50 border-l-4 border-red-400">
                            <p class="text-red-700">Tỷ lệ hoàn thành dự án còn thấp ({{ round($completionRate, 1) }}%). Hãy tập trung vào việc hoàn thành các dự án đang thực hiện trước khi bắt đầu dự án mới.</p>
                        </div>
                    @elseif($completionRate < 80)
                        <div class="p-3 bg-yellow-50 border-l-4 border-yellow-400">
                            <p class="text-yellow-700">Tỷ lệ hoàn thành dự án ở mức trung bình ({{ round($completionRate, 1) }}%). Bạn có thể cải thiện bằng cách chia nhỏ dự án thành các công việc cụ thể hơn.</p>
                        </div>
                    @else
                        <div class="p-3 bg-green-50 border-l-4 border-green-400">
                            <p class="text-green-700">Xuất sắc! Tỷ lệ hoàn thành dự án rất cao ({{ round($completionRate, 1) }}%). Hãy tiếp tục duy trì phong độ này.</p>
                        </div>
                    @endif
                    
                    @if($overdueCount > 0)
                        <div class="p-3 bg-red-50 border-l-4 border-red-400">
                            <p class="text-red-700">Có {{ $overdueCount }} dự án quá hạn. Hãy ưu tiên hoàn thành những dự án này hoặc điều chỉnh thời hạn cho phù hợp.</p>
                        </div>
                    @endif
                    
                    @if($taskCompletionRate > 90)
                        <div class="p-3 bg-green-50 border-l-4 border-green-400">
                            <p class="text-green-700">Tuyệt vời! Bạn có tỷ lệ hoàn thành công việc rất cao ({{ round($taskCompletionRate, 1) }}%).</p>
                        </div>
                    @endif
                    
                    @if($totalProjects == 0)
                        <div class="p-3 bg-blue-50 border-l-4 border-blue-400">
                            <p class="text-blue-700">Hãy bắt đầu bằng việc tạo dự án đầu tiên của bạn! Đặt ra mục tiêu cụ thể và chia nhỏ thành các công việc có thể thực hiện được.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Print styles -->
    <style media="print">
        .no-print { display: none !important; }
        .print\:shadow-none { box-shadow: none !important; }
        body { font-size: 12px; }
        .py-12 { padding-top: 1rem; padding-bottom: 1rem; }
    </style>
</x-app-layout>
