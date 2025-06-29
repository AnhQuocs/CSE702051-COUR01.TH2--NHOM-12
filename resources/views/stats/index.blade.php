<x-app-layout>
    <x-slot name="header">        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Thống kê hoạt động
            </h2>            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    Dữ liệu cập nhật: {{ now()->format('d/m/Y H:i') }}
                </div>                <div class="flex items-center space-x-2">
                    <!-- Report button -->
                    <a href="{{ route('stats.report') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200 flex items-center">
                        Báo cáo chi tiết
                    </a>
                    
                    <!-- Export dropdown -->
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200 flex items-center">
                            Xuất dữ liệu
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                <a href="{{ route('stats.export', 'json') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Xuất JSON
                                </a>
                                <a href="{{ route('stats.export', 'csv') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Xuất CSV
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <button onclick="location.reload()" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors duration-200">
                        Làm mới
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Custom CSS for Stats -->
    <link rel="stylesheet" href="{{ asset('css/stats.css') }}">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
              <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Projects -->
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6 cursor-pointer" onclick="window.location.href='{{ route('projects.index') }}'">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7l-7 7-7-7m14 18l-7-7-7 7"></path>
                                </svg>
                            </div>
                        </div>                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Tổng dự án</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalProjects }}</p>
                            @if(isset($previousPeriodData['projects']['change']))
                                <div class="flex items-center text-xs">
                                    @if($previousPeriodData['projects']['change'] > 0)
                                        <svg class="w-3 h-3 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                                        </svg>
                                        <span class="text-green-600">+{{ $previousPeriodData['projects']['change'] }}</span>
                                    @elseif($previousPeriodData['projects']['change'] < 0)
                                        <svg class="w-3 h-3 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"></path>
                                        </svg>
                                        <span class="text-red-600">{{ $previousPeriodData['projects']['change'] }}</span>
                                    @else
                                        <span class="text-gray-500">Không đổi</span>
                                    @endif
                                    <span class="text-gray-500 ml-1">so với 30 ngày trước</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Completed Projects -->
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6 cursor-pointer" onclick="window.location.href='{{ route('projects.index', ['status' => 'completed']) }}'">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Dự án hoàn thành</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $completedProjects }}</p>
                            @if($totalProjects > 0)
                                <p class="text-xs text-gray-500">{{ round(($completedProjects / $totalProjects) * 100, 1) }}% hoàn thành</p>
                            @endif
                            @if(isset($previousPeriodData['completed_projects']['change']))
                                <div class="flex items-center text-xs">
                                    @if($previousPeriodData['completed_projects']['change'] > 0)
                                        <svg class="w-3 h-3 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                                        </svg>
                                        <span class="text-green-600">+{{ $previousPeriodData['completed_projects']['change'] }}</span>
                                    @elseif($previousPeriodData['completed_projects']['change'] < 0)
                                        <svg class="w-3 h-3 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"></path>
                                        </svg>
                                        <span class="text-red-600">{{ $previousPeriodData['completed_projects']['change'] }}</span>
                                    @else
                                        <span class="text-gray-500">Không đổi</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>                <!-- Total Subtasks -->
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Tổng công việc</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalSubtasks }}</p>
                        </div>
                    </div>
                </div>

                <!-- Completed Subtasks -->
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Công việc hoàn thành</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $completedSubtasks }}</p>
                            @if($totalSubtasks > 0)
                                <p class="text-xs text-gray-500">{{ round(($completedSubtasks / $totalSubtasks) * 100, 1) }}% hoàn thành</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Project Status Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Dự án theo trạng thái</h3>
                        @if($totalProjects > 0)
                            <div class="text-sm text-gray-500">{{ $totalProjects }} dự án</div>
                        @endif
                    </div>
                    @if($totalProjects > 0)
                        <div class="space-y-3">
                            @php
                                $statusLabels = [
                                    'not_planned' => ['Chưa lên kế hoạch', 'bg-gray-500'],
                                    'not_started' => ['Chưa bắt đầu', 'bg-yellow-500'],
                                    'in_progress' => ['Đang thực hiện', 'bg-blue-500'],
                                    'completed' => ['Hoàn thành', 'bg-green-500'],
                                    'overdue' => ['Quá hạn', 'bg-red-500']
                                ];
                            @endphp
                            
                            @foreach($projectsByStatus as $status => $count)
                                @if($count > 0)
                                    @php [$label, $color] = $statusLabels[$status] ?? [$status, 'bg-gray-500'] @endphp
                                    <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition-colors duration-150 cursor-pointer" onclick="window.location.href='{{ route('projects.index', ['status' => $status]) }}'">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 {{ $color }} rounded-full mr-3"></div>
                                            <span class="text-sm text-gray-700">{{ $label }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900 mr-2">{{ $count }}</span>
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="{{ $color }} h-2 rounded-full" style="width: {{ ($count / $totalProjects) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có dự án</h3>
                            <p class="mt-1 text-sm text-gray-500">Hãy tạo dự án đầu tiên của bạn.</p>
                            <div class="mt-6">
                                <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Tạo dự án mới
                                </a>
                            </div>
                        </div>
                    @endif
                </div>                <!-- Priority Distribution -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Phân bổ mức độ ưu tiên</h3>
                        @if($totalProjects > 0)
                            <div class="text-sm text-gray-500">{{ $totalProjects }} dự án</div>
                        @endif
                    </div>
                    @if($totalProjects > 0)
                        <div class="space-y-3">
                            @php
                                $priorityLabels = [
                                    'low' => ['Thấp', 'bg-green-500'],
                                    'medium' => ['Trung bình', 'bg-yellow-500'],
                                    'high' => ['Cao', 'bg-red-500']
                                ];
                            @endphp
                            
                            @foreach($priorityLabels as $priority => [$label, $color])
                                @php $count = $projectsByPriority[$priority] ?? 0 @endphp
                                <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition-colors duration-150 cursor-pointer" onclick="window.location.href='{{ route('projects.index', ['priority' => $priority]) }}'">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 {{ $color }} rounded-full mr-3"></div>
                                        <span class="text-sm text-gray-700">{{ $label }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900 mr-2">{{ $count }}</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="{{ $color }} h-2 rounded-full" style="width: {{ $totalProjects > 0 ? ($count / $totalProjects) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có dự án</h3>
                            <p class="mt-1 text-sm text-gray-500">Tạo dự án để xem phân bổ ưu tiên.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Weekly Activity -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hoạt động 7 ngày qua</h3>
                    <div class="space-y-2">
                        @foreach($weeklyData as $day)
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600 w-12">{{ $day['day'] }}</div>
                                <div class="text-xs text-gray-500 w-12">{{ $day['date'] }}</div>
                                <div class="flex-1 mx-3">
                                    <div class="bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $day['completed'] > 0 ? min(($day['completed'] / max(array_column($weeklyData, 'completed'))) * 100, 100) : 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900 w-8 text-right">{{ $day['completed'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Monthly Trend -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Xu hướng 12 tháng</h3>
                    <div class="relative">
                        <canvas id="monthlyChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Projects -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dự án gần đây</h3>
                    @if($recentProjects->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentProjects as $project)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <a href="{{ route('projects.show', $project) }}" class="hover:text-blue-600">
                                                {{ $project->title }}
                                            </a>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $project->created_at->diffForHumans() }}</p>
                                    </div>
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
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$project->final_status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$project->final_status] ?? $project->final_status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Chưa có dự án nào được tạo gần đây.</p>
                    @endif
                </div>

                <!-- Recent Completed Tasks -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Công việc hoàn thành gần đây</h3>
                    @if($recentSubtasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentSubtasks as $subtask)
                                <div class="flex items-start space-x-3 py-2">
                                    <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $subtask->title }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $subtask->project->title }} • {{ $subtask->updated_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Chưa có công việc nào hoàn thành gần đây.</p>
                    @endif
                </div>
            </div>            <!-- Insights -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">📈 Thông tin chi tiết</h3>
                    <div class="text-sm text-gray-500">Phân tích hiệu suất</div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">
                            @if($averageProjectDuration)
                                {{ round($averageProjectDuration) }}
                            @else
                                --
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 mt-1">Thời gian trung bình hoàn thành dự án (ngày)</div>
                    </div>
                    
                    <div class="text-center p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">
                            @if($totalSubtasks > 0)
                                {{ round(($completedSubtasks / $totalSubtasks) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 mt-1">Tỷ lệ hoàn thành công việc</div>
                    </div>
                    
                    <div class="text-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $recentSubtasks->where('updated_at', '>=', Carbon\Carbon::now()->subDays(7))->count() }}
                        </div>
                        <div class="text-sm text-gray-600 mt-1">Công việc hoàn thành tuần này</div>
                    </div>
                    
                    <div class="text-center p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600">
                            @if($totalProjects > 0)
                                {{ round(($totalSubtasks / $totalProjects), 1) }}
                            @else
                                0
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 mt-1">Số công việc trung bình mỗi dự án</div>
                    </div>
                </div>
                
                <!-- Productivity Insights -->
                @if($totalProjects > 0 || $totalSubtasks > 0)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">🔍 Phân tích hiệu suất</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($totalProjects > 0 && ($completedProjects / $totalProjects) >= 0.8)
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                </svg>
                                            </div>
                                        @elseif($totalProjects > 0 && ($completedProjects / $totalProjects) >= 0.5)
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Tỷ lệ hoàn thành dự án</p>
                                        <p class="text-xs text-gray-500">
                                            @if($totalProjects > 0 && ($completedProjects / $totalProjects) >= 0.8)
                                                Xuất sắc! Bạn có tỷ lệ hoàn thành dự án rất cao.
                                            @elseif($totalProjects > 0 && ($completedProjects / $totalProjects) >= 0.5)
                                                Tốt! Hãy tiếp tục nỗ lực để hoàn thành thêm nhiều dự án.
                                            @elseif($totalProjects > 0)
                                                Cần cải thiện. Hãy tập trung hoàn thành các dự án đang thực hiện.
                                            @else
                                                Hãy bắt đầu bằng việc tạo dự án đầu tiên!
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @php 
                                            $overdueCount = $projectsByStatus['overdue'] ?? 0;
                                        @endphp
                                        @if($overdueCount == 0)
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @elseif($overdueCount <= 2)
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Quản lý thời gian</p>
                                        <p class="text-xs text-gray-500">
                                            @if($overdueCount == 0)
                                                Tuyệt vời! Không có dự án nào quá hạn.
                                            @elseif($overdueCount <= 2)
                                                Có {{ $overdueCount }} dự án quá hạn. Hãy ưu tiên hoàn thành chúng.
                                            @else
                                                Cảnh báo! {{ $overdueCount }} dự án quá hạn. Cần xem xét lại kế hoạch.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>    <!-- Chart.js for monthly trend -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Stats JS -->
    <script src="{{ asset('js/stats.js') }}"></script>
    <script>
        // Monthly trend chart
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
                datasets: [{
                    label: 'Dự án mới',
                    data: {!! json_encode(array_column($monthlyData, 'projects')) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    fill: true
                }, {
                    label: 'Công việc hoàn thành',
                    data: {!! json_encode(array_column($monthlyData, 'completed_subtasks')) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    </script>
</x-app-layout>
