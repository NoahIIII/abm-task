<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponseTrait;

    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    public function index()
    {
        $tasks = $this->taskService->listTasks();
        return $this->apiResponse($tasks, 200, 'Tasks retrieved successfully');
    }

    public function store(TaskStoreRequest $request)
    {
        $task = $this->taskService->createTask($request->validated());
        return $this->apiResponse($task, 201, 'Task created successfully');
    }

    public function update(TaskUpdateRequest $request, int $taskId)
    {
        $task = $this->taskService->findUserTaskOrFail($taskId);
        $updatedTask = $this->taskService->updateTask($task, $request->validated());
        return $this->apiResponse($updatedTask, 200, 'Task updated successfully');
    }

    public function destroy(int $taskId)
    {
        $task = $this->taskService->findUserTaskOrFail($taskId);
        $this->taskService->deleteTask($task);
        return $this->apiResponse([], 200, 'Task deleted successfully');
    }
}
