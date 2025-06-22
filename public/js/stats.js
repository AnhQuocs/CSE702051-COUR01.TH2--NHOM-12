// Statistics page interactions
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips for charts
    initializeTooltips();
    
    // Add smooth animations for progress bars
    animateProgressBars();
    
    // Add loading states for refresh button
    initializeRefreshButton();
    
    // Add keyboard navigation
    initializeKeyboardNavigation();
    
    // Initialize chart interactions
    initializeChartInteractions();
});

function initializeTooltips() {
    // Add tooltips to progress bars and charts
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
        bar.addEventListener('mouseenter', function() {
            const width = this.style.width;
            showTooltip(this, `${width} completed`);
        });
        
        bar.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
}

function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill');
    
    // Use Intersection Observer to animate when bars come into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width;
                bar.style.width = '0%';
                
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
                
                observer.unobserve(bar);
            }
        });
    });
    
    progressBars.forEach(bar => observer.observe(bar));
}

function initializeRefreshButton() {
    const refreshButton = document.querySelector('[onclick="location.reload()"]');
    if (refreshButton) {
        refreshButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add loading state
            this.innerHTML = '⏳ Đang tải...';
            this.disabled = true;
            
            // Add loading animation to cards
            const cards = document.querySelectorAll('.stats-card');
            cards.forEach(card => card.classList.add('loading'));
            
            // Reload after a short delay for better UX
            setTimeout(() => {
                location.reload();
            }, 500);
        });
    }
}

function initializeKeyboardNavigation() {
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // R for refresh
        if (e.key === 'r' && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            location.reload();
        }
        
        // P for projects
        if (e.key === 'p' && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            window.location.href = '/projects';
        }
        
        // D for dashboard
        if (e.key === 'd' && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            window.location.href = '/dashboard';
        }
    });
}

function initializeChartInteractions() {
    // Add click handlers for status and priority charts
    const statusItems = document.querySelectorAll('[onclick*="projects.index"]');
    statusItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8fafc';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Add animation to insight cards
    const insightCards = document.querySelectorAll('.insight-card');
    insightCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
}

// Utility functions
function showTooltip(element, text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.innerHTML = text;
    tooltip.style.cssText = `
        position: absolute;
        background: #1f2937;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + 'px';
    tooltip.style.top = (rect.top - 30) + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

// Chart.js customizations
if (window.Chart) {
    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.color = '#374151';
    
    // Add custom animations
    Chart.defaults.animation = {
        duration: 2000,
        easing: 'easeInOutQuart'
    };
}

// Export function for external use
window.StatsHelper = {
    refreshData: function() {
        location.reload();
    },
    
    navigateToProjects: function(filter = '') {
        const url = filter ? `/projects?${filter}` : '/projects';
        window.location.href = url;
    },
    
    showMetricDetail: function(metric, value) {
        alert(`${metric}: ${value}`);
    }
};
