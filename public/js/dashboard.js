console.log('Dashboard JS loaded successfully');

// Toggle subtask completion in dashboard
function toggleSubtaskInDashboard(subtaskId, projectId) {
    console.log('Function called - toggleSubtaskInDashboard');
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var checkbox = document.querySelector('[data-subtask-id="' + subtaskId + '"] input[type="checkbox"]');
    
    console.log('Toggling subtask:', subtaskId, 'for project:', projectId);
    
    fetch('/subtasks/' + subtaskId + '/toggle', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(function(response) {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        console.log('Response data:', data);
        if (data.success) {
            // Update the specific subtask item
            var subtaskItem = document.querySelector('[data-subtask-id="' + subtaskId + '"]');
            var text = subtaskItem.querySelector('span.flex-1');
            
            // Update checkbox state to match server response
            checkbox.checked = data.subtask.is_completed;
            
            if (data.subtask.is_completed) {
                text.classList.add('line-through', 'text-gray-500');
                text.classList.remove('text-gray-700');
            } else {
                text.classList.remove('line-through', 'text-gray-500');
                text.classList.add('text-gray-700');
            }
            
            // Update progress bar for this project
            updateProjectProgress(projectId, data.project);
        } else {
            alert('Có lỗi xảy ra khi cập nhật trạng thái công việc!');
            // Revert checkbox state
            checkbox.checked = !checkbox.checked;
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái công việc: ' + error.message);
        // Revert checkbox state
        checkbox.checked = !checkbox.checked;
    });
}

// Update project progress and status
function updateProjectProgress(projectId, projectData) {
    var projectCard = document.querySelector('[data-project-id="' + projectId + '"]');
    if (!projectCard) return;

    // Update progress bar
    var progressBar = projectCard.querySelector('.bg-blue-600');
    var progressText = projectCard.querySelector('.font-medium.text-gray-900');
    var progressSubtext = projectCard.querySelector('.text-xs.text-gray-500');
    
    if (progressBar && progressText) {
        progressBar.style.width = projectData.progress_percentage + '%';
        progressText.textContent = projectData.progress_percentage + '%';
    }
    
    // Update subtasks count
    if (progressSubtext && projectData.subtasks_count > 0) {
        progressSubtext.textContent = projectData.completed_subtasks_count + '/' + projectData.subtasks_count + ' công việc đã hoàn thành';
    }

    // Update status badge
    var statusBadge = projectCard.querySelector('.px-3.py-1');
    if (statusBadge && projectData.final_status) {
        var statusLabels = {
            'not_planned': 'Chưa lên kế hoạch',
            'not_started': 'Chưa bắt đầu',
            'in_progress': 'Đang thực hiện',
            'completed': 'Hoàn thành',
            'overdue': 'Quá hạn'
        };
        
        var statusColors = {
            'not_planned': 'bg-gray-100 text-gray-800',
            'not_started': 'bg-yellow-100 text-yellow-800',
            'in_progress': 'bg-blue-100 text-blue-800',
            'completed': 'bg-green-100 text-green-800',
            'overdue': 'bg-red-100 text-red-800'
        };
        
        statusBadge.className = 'inline-flex px-3 py-1 text-xs font-medium rounded-full ' + (statusColors[projectData.final_status] || 'bg-gray-100 text-gray-800');
        statusBadge.textContent = statusLabels[projectData.final_status] || projectData.final_status;
    }
}
