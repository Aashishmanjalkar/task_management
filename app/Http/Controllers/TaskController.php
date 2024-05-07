<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    /**
     * Get logged in user tasks
     */
    public function getTask()
    {
        try {
            $user = Auth::user();
            $tasks = User::with('tasks')->where('id', $user->id)->get();
            return response()->json($tasks);
        } catch (\Exception $e) {
            return response()->json(['response_code' => 500, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get all tasks along with tagged user
     */
    public function getAllTask()
    {
        try {
            $tasks = Task::with('users')->get();
            return response()->json($tasks);
        } catch (\Exception $e) {
            return response()->json(['response_code' => 500, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get tasks if filter parameters are available then filter according to it
     */
    public function getFilteredTask(Request $request)
    {
        try {
            $tasks = Task::query();

            $tasks->when($request->status, function ($query) use ($request) {
                return $query->where('status', '=', $request->status);
            });
            $tasks->when($request->date, function ($query) use ($request) {
                return $query->whereDate('due_date', '=', $request->date);
            });

            $tasks->when($request->user_id, function ($query) use ($request) {
                return $query->whereHas('users', function ($query) use ($request) {
                    $query->where('user_id', $request->user_id);
                });
            });

            $tasks = $tasks->get();
            return response()->json($tasks);
        } catch (\Exception $e) {
            return response()->json(['response_code' => 500, 'error' => $e->getMessage()]);
        }
    }

    /**
     *  Add and update the task
     */
    public function addUpdateTask(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'due_date' => 'required|date',
                'status' => 'required|in:pending,in progress,completed'
            ], [
                'status.in' => 'Invalid status. Status must be one of: pending, in progress, completed.',
            ]);

            if ($validator->fails()) {
                $response = ['response_code' => 422, 'error' => $validator->errors()->all()];
                return response()->json($response);
            }

            if ($request->task_id) {
                //Update task
                $task = Task::where('id', $request->task_id)->first();
                $message = 'Task updated successfully';
                if (!$task) $task = new Task();
            } else {
                //Add task
                $task = new Task();
                $message = 'Task added successfully';
            }

            $task->title = $request->title;
            $task->description = $request->description;
            $task->due_date = $request->due_date;
            $task->status = $request->status;
            $task->save();

            return response()->json(['response_code' => 200, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['response_code' => 500, 'error' => $e->getMessage()]);
        }
    }

    /**
     *  Update status of the task
     */
    public function updateStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|exists:tasks,id',
                'status' => 'required|in:pending,in progress,completed',
            ], [
                'task_id.exists' => 'Task not found',
                'status.in' => 'Invalid status. Status must be one of: pending, in progress, completed.',
            ]);

            if ($validator->fails()) {
                $response = ['response_code' => 422, 'error' => $validator->errors()->all()];
                return response()->json($response);
            }

            Task::where('id', $request->task_id)->update(['status' => $request->status]);

            return response()->json(['response_code' => 200, 'message' => 'Status updated.']);
        } catch (\Exception $e) {
            return response()->json(['response_code' => 500, 'error' => $e->getMessage()]);
        }
    }

    /**
     *  Deleting the task
     */
    public function deleteTask(Request $request)
    {
        try {
            $task = Task::where('id', $request->id)->first();
            if (!$task) {
                return response()->json(['response_code' => 404, 'error' => "Task not found."]);
            }
            $task->delete();
            return response()->json(['response_code' => 200, 'message' => "Task deleted successfully."]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     *  Add users to the task
     */
    public function addUserToTask(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required',
                'tagged_user' => 'required'
            ]);

            if ($validator->fails()) {
                $response = ['response_code' => 422, 'error' => $validator->errors()->all()];
                return response()->json($response);
            }

            $task = Task::where('id', $request->task_id)->first();

            if (!$task) return response()->json(['response_code' => 404, 'message' => 'Task not found']);

            $taggedUser = explode(",", $request->tagged_user);
            foreach ($taggedUser as $user) {
                $taskUser = TaskUser::where('task_id', $task->id)->where('user_id', $user)->first();
                if (!$taskUser) {
                    $taskUser = new TaskUser();
                    $taskUser->task_id = $task->id;
                    $taskUser->user_id = $user;
                    $taskUser->save();
                }
            }

            return response()->json(['response_code' => 200, 'message' => 'User added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['response_code' => 500, 'error' => $e->getMessage()]);
        }
    }

    /**
     *  unassign the user from the task
     */
    public function removeUserFromTask(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required',
                'tagged_user' => 'required'
            ]);

            if ($validator->fails()) {
                $response = ['response_code' => 422, 'error' => $validator->errors()->all()];
                return response()->json($response);
            }

            $task = Task::where('id', $request->task_id)->first();

            if (!$task) return response()->json(['response_code' => 404, 'message' => 'Task not found']);

            $taggedUser = explode(",", $request->tagged_user);

            foreach ($taggedUser as $user) {
                $taskUser = TaskUser::where('task_id', $task->id)->where('user_id', $user);
                if ($taskUser) {
                    // Delete the record
                    $taskUser->delete();
                }
            }

            return response()->json(['response_code' => 200, 'message' => 'User removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['response_code' => 500, 'error' => $e->getMessage()]);
        }
    }
}
