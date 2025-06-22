<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Subtask;
use App\Models\User;
use App\Http\Controllers\SubtaskController;
use Illuminate\Support\Facades\Auth;

$user = User::first();
Auth::login($user);

$subtask = Subtask::find('0197902a-627c-7139-946b-a2a06f80babc');
echo "Subtask found: " . $subtask->title . PHP_EOL;
echo "Current status: " . ($subtask->is_completed ? 'completed' : 'not completed') . PHP_EOL;

echo "User ID: " . $user->id . PHP_EOL;
echo "Project owner ID: " . $subtask->project->user_id . PHP_EOL;

// Try with correct user
$correctUser = User::find($subtask->project->user_id);
if ($correctUser) {
    Auth::login($correctUser);
    echo "Logged in as correct user: " . $correctUser->name . PHP_EOL;
    
    $controller2 = new SubtaskController();
    $response = $controller2->toggle($subtask);
    echo "New response status: " . $response->getStatusCode() . PHP_EOL;
    echo "New response content: " . $response->getContent() . PHP_EOL;
}
