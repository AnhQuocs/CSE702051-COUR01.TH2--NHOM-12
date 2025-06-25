<!DOCTYPE html>
<html>
<head>
    <title>Quick Toggle Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-box { border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
        button { padding: 10px 20px; margin: 5px; }
    </style>
</head>
<body>
    <h1>Subtask Toggle Test</h1>
    
    <div class="test-box">
        <h3>Test Subtask: 0197902a-627c-7139-946b-a2a06f80babc</h3>
        <button onclick="toggleSubtask()">Toggle Subtask</button>
        <button onclick="checkStatus()">Check Current Status</button>
        <div id="results"></div>
    </div>

    <script>
        const subtaskId = '0197902a-627c-7139-946b-a2a06f80babc';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        async function toggleSubtask() {
            const results = document.getElementById('results');
            results.innerHTML = '<p>🔄 Toggling subtask...</p>';
            
            try {
                const response = await fetch(`/subtasks/${subtaskId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Response:', response);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('Data:', data);
                
                results.innerHTML = `
                    <div class="test-box success">
                        <h4>✅ Toggle Successful!</h4>
                        <p><strong>Subtask ID:</strong> ${data.subtask.id}</p>
                        <p><strong>New Status:</strong> ${data.subtask.is_completed ? 'Completed' : 'Not Completed'}</p>
                        <p><strong>Project Progress:</strong> ${data.project.progress_percentage}%</p>
                        <p><strong>Project Status:</strong> ${data.project.final_status}</p>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    </div>
                `;
                
            } catch (error) {
                console.error('Error:', error);
                results.innerHTML = `
                    <div class="test-box error">
                        <h4>❌ Toggle Failed!</h4>
                        <p><strong>Error:</strong> ${error.message}</p>
                    </div>
                `;
            }
        }
        
        async function checkStatus() {
            const results = document.getElementById('results');
            results.innerHTML = '<p>🔍 Checking status...</p>';
            
            try {
                const response = await fetch(`/test-simple`);
                const simple = await response.json();
                
                results.innerHTML = `
                    <div class="test-box">
                        <h4>📊 API Status</h4>
                        <p><strong>API Working:</strong> ${simple.success ? 'Yes' : 'No'}</p>
                        <p><strong>Time:</strong> ${simple.time}</p>
                        <p><strong>CSRF Token:</strong> ${csrfToken ? 'Present' : 'Missing'}</p>
                    </div>
                `;
                
            } catch (error) {
                results.innerHTML = `
                    <div class="test-box error">
                        <h4>❌ Check Failed!</h4>
                        <p><strong>Error:</strong> ${error.message}</p>
                    </div>
                `;
            }
        }
        
        // Auto check on load
        window.onload = checkStatus;
    </script>
</body>
</html>
