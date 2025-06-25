<!-- Dark Mode Toggle -->
<button 
    id="dark-mode-toggle" 
    class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors"
    title="Chế độ tối"
    onclick="showDarkModeMessage()"
>
    <!-- Moon Icon -->
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
</button>

<script>
function showDarkModeMessage() {
    // Tạo thông báo overlay
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    overlay.innerHTML = `        <div class="bg-white rounded-lg p-6 max-w-sm mx-4 text-center shadow-2xl border-2 border-gray-200"
             style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-200">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Chế độ tối</h3>
            <p class="text-gray-600 mb-4">Tính năng đang được phát triển và sẽ có mặt trong phiên bản tiếp theo!</p>
            <button 
                onclick="closeDarkModeMessage()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors border border-blue-700 shadow-md"
            >
                Đã hiểu
            </button>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Đóng khi click outside
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeDarkModeMessage();
        }
    });
}

function closeDarkModeMessage() {
    const overlay = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
    if (overlay) {
        overlay.remove();
    }
}

// Đóng bằng phím ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDarkModeMessage();
    }
});
</script>
