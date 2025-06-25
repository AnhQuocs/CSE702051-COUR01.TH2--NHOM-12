// Enhanced Drag & Drop for Subtasks
let draggedElement = null;
let draggedIndex = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing drag and drop...');
    initDragAndDrop();
});

function initDragAndDrop() {
    console.log('Initializing drag and drop...');
    
    const container = document.getElementById('sortable-subtasks');
    if (!container) {
        console.log('Container not found');
        return;
    }
    
    const subtaskItems = container.querySelectorAll('.subtask-item[draggable="true"]');
    console.log('Found subtask items:', subtaskItems.length);
    
    // Add container event listeners
    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        
        const afterElement = getDragAfterElement(container, e.clientY);
        if (afterElement == null) {
            container.appendChild(draggedElement);
        } else {
            container.insertBefore(draggedElement, afterElement);
        }
    });
    
    container.addEventListener('drop', function(e) {
        e.preventDefault();
        console.log('Drop event on container');
        updateSubtasksOrder();
    });
    
    // Add item event listeners
    subtaskItems.forEach((item, index) => {
        // Store original index
        item.setAttribute('data-original-index', index);
        
        item.addEventListener('dragstart', function(e) {
            draggedElement = this;
            draggedIndex = Array.from(container.children).indexOf(this);
            
            this.classList.add('dragging');
            console.log('Drag started for subtask:', this.dataset.subtaskId, 'at index:', draggedIndex);
            
            // Set drag effect
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
        });
        
        item.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            
            // Remove all drag indicators
            const items = container.querySelectorAll('.subtask-item');
            items.forEach(item => {
                item.classList.remove('drag-over-top', 'drag-over-bottom');
            });
            
            draggedElement = null;
            draggedIndex = null;
            console.log('Drag ended');
        });
        
        item.addEventListener('dragenter', function(e) {
            e.preventDefault();
            if (this !== draggedElement) {
                this.classList.add('drag-over');
            }
        });
        
        item.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });
    });
}

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.subtask-item:not(.dragging)')];
    
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function updateSubtasksOrder() {
    const container = document.getElementById('sortable-subtasks');
    if (!container) {
        console.error('Container not found');
        return;
    }
    
    const subtaskIds = Array.from(container.children).map(item => 
        item.getAttribute('data-subtask-id')
    );
    
    console.log('New order:', subtaskIds);
    
    // Get project ID from meta or global variable
    const projectId = window.projectId || document.querySelector('[data-project-id]')?.dataset.projectId;
    
    if (!projectId) {
        console.error('Project ID not found');
        return;
    }
    
    fetch(`/projects/${projectId}/subtasks/order`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            subtask_ids: subtaskIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Order updated successfully');
            showToast('Đã cập nhật thứ tự công việc', 'success');
        } else {
            console.error('Failed to update order:', data.error);
            showToast('Lỗi khi cập nhật thứ tự', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Lỗi kết nối', 'error');
    });
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-md shadow-lg text-white transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
