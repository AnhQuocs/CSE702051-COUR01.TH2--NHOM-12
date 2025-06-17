<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Project Reminder</title>
</head>
<body>
    <h2>Project Reminder</h2>
    <p>Hi {{ $project->user->username ?? $project->user->email }},</p>
    <p>This is a reminder for your project: <strong>{{ $project->title }}</strong>.</p>
    <p>Description: {{ $project->description }}</p>
    <p>Priority: {{ $project->priority }}</p>
    <p>Status: {{ $project->status }}</p>
    <p>Deadline: {{ $project->deadline }}</p>
    <p>Please check your project and update its status if needed.</p>
    <br>
    <p>Best regards,<br>My Project Hub</p>
</body>
</html>
