<!-- Quick Stats Widget -->
<div class="bg-white rounded-lg shadow-md p-6">    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Thống kê nhanh</h3>
        <a href="{{ route('stats.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Xem chi tiết →
        </a>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $totalProjects ?? 0 }}</div>
            <div class="text-xs text-gray-500">Tổng dự án</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $completedProjects ?? 0 }}</div>
            <div class="text-xs text-gray-500">Hoàn thành</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $totalSubtasks ?? 0 }}</div>
            <div class="text-xs text-gray-500">Tổng công việc</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $completedSubtasks ?? 0 }}</div>
            <div class="text-xs text-gray-500">CV hoàn thành</div>
        </div>
    </div>
    
    @if(($totalProjects ?? 0) > 0)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tỷ lệ hoàn thành</span>
                <span class="font-medium">{{ round((($completedProjects ?? 0) / ($totalProjects ?? 1)) * 100, 1) }}%</span>
            </div>
            <div class="mt-2 bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ round((($completedProjects ?? 0) / ($totalProjects ?? 1)) * 100, 1) }}%"></div>
            </div>
        </div>
    @endif
</div>
