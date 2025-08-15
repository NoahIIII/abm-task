<?php
namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    public function listTasks()
    {
        return Task::where('user_id', Auth::id())->get();
    }

    public function createTask(array $data)
    {
        $data['user_id'] = Auth::id();
        return Task::create($data);
    }

    public function findUserTaskOrFail(int $taskId)
    {
        return Task::where('id', $taskId)
                   ->where('user_id', Auth::id())
                   ->firstOrFail();
    }

    public function updateTask(Task $task, array $data)
    {
        $task->update($data);
        return $task;
    }

    public function deleteTask(Task $task)
    {
        return $task->delete();
    }
}
