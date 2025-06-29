<!-- Dark Mode Toggle -->
<button 
    id="dark-mode-toggle" 
    class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors"
    title="Dark Mode (Coming Soon)"
>
    <!-- Moon Icon -->
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
      darkModeToggle.addEventListener('click', function() {
        // Show coming soon message
        alert('Tính năng Dark Mode đang được phát triển!\n\nVui lòng chờ phiên bản tiếp theo.');
    });
});
</script>
