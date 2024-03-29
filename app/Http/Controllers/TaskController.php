<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
// use App\Http\Resources\TaskResource;
// use Illuminate\Http\Response;
use App\Models\TaskFile;

class TaskController extends Controller
{


    public function __construct()
    {
    }

    public function index()
    {
        $pageTitle = 'Task List'; // Ditambahkan
        $tasks = Task::all();
        if (Gate::allows('viewAnyTask', Task::class)) {
            $tasks = Task::all();
        } else {
            $tasks = Task::where('user_id', Auth::user()->id)->get();
        }
        return view('tasks.index', [
            'pageTitle' => $pageTitle, //Ditambahkan
            'tasks' => $tasks,
        ]);
        // return response()->json([
        //     'code' => 200,
        //     'message' => 'Task successfully',
        //     'data' => TaskResource::collection($tasks),
        // ]);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Task';
        $task = Task::find($id);

        // return response()->json([
        //     'data' => new TaskResource($task),
        // ], Response::HTTP_OK);

        Gate::authorize('update', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }


        return view('tasks.edit', ['pageTitle' => $pageTitle, 'task' => $task]);
    }

    public function create($status = null)
    {
        $pageTitle = "Create Task";
        return view('tasks.create', ['pageTitle' => $pageTitle, 'status' => $status]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'due_date' => 'required',
            'status' => 'required',
            'file' => ['max:5000', 'mimes:pdf,jpeg,png'],
        ], [
            'file.max' => 'The file size exceeds 5 MB',
            'file.mimes' => 'File type must be: pdf, jpeg, png',
        ],

        $request->all()
    );

        DB::beginTransaction();

        try {
            $task = Task::create([
                'name' => $request->name,
                'detail' => $request->detail,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'user_id' => Auth::user()->id,
            ]);

            $file = $request->file('file');

            if ($file) {
                $filename = $file->getClientOriginalName();
                $path = $file->storePubliclyAs('tasks', $file->hashName(), 'public');

                TaskFile::create([
                    'task_id' => $task->id,
                    'filename' => $filename,
                    'path' => $path,
                ]);
            }
            $pageTitle = 'Task List'; // Ditambahkan
            $tasks = Task::all();
            DB::commit();

            // return response()->json([
            //     'code' => 200,
            //     'message' => 'Task created successfully',
            // ]);
            return redirect()->route('tasks.index');
        } catch (\Throwable $th) {
            DB::rollBack();

            // return response()->json([
            //     'code' => 200,
            //     'message' => 'Task created unsuccessfully',
            // ]);
            return redirect()
                ->route('tasks.create')
                ->with('error', $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        Gate::authorize('update', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }
        // Gate::authorize('update', $task);
        $task->update([
            'name' => $request->name,
            'detail' => $request->detail,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);


        // return response()->json([
        //     'code' => 200,
        //     'message' => 'Task update successfully',
        // ]);
        return redirect()->route('tasks.index');
    }

    public function delete($id)
    {
        $deleteTask = 'delete task';
        $task = Task::findOrFail($id);

        Gate::authorize('delete', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('deleteAnyTask', Task::class);
        }

        return view('tasks.delete', ['pageTitle' => $deleteTask, 'task' => $task]);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        Gate::authorize('delete', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('deleteAnyTask', Task::class);
        }

        $task->delete();
        return redirect()->route('tasks.index');
        // return response()->json([

        //     'message' => 'Tasks' . $task->name . 'Delete data successfully',

        // ]);
    }

    public function progress()
    {
        $title = 'Task Progress';

        if (Gate::allows('viewAnyTask', Task::class)) {
            $tasks = Task::all();
        } else {
            $tasks = Task::where('user_id', Auth::user()->id)->get();
        }
        $tasks = Task::all();
        $filteredTasks = $tasks->groupBy('status');



        $tasks = [
            Task::STATUS_NOT_STARTED => $filteredTasks->get(
                Task::STATUS_NOT_STARTED,
                []
            ),
            Task::STATUS_IN_PROGRESS => $filteredTasks->get(
                Task::STATUS_IN_PROGRESS,
                []
            ),
            Task::STATUS_IN_REVIEW => $filteredTasks->get(
                Task::STATUS_IN_REVIEW,
                []
            ),
            Task::STATUS_COMPLETED => $filteredTasks->get(
                Task::STATUS_COMPLETED,
                []
            ),
        ];

        return view('tasks.progress', [
            'pageTitle' => $title,
            'tasks' => $tasks,
        ]);
    }

    public function move(int $id, Request $request)
    {
        $task = Task::findOrFail($id);

        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }


        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.progress');
    }

    public function updateStatusFromIndex($id)
    {
        $task = Task::find($id);
        Gate::authorize('update', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }
        $task->update([
            'status' => Task::STATUS_COMPLETED
        ]);

        return redirect()->route('tasks.index');
    }

    public function updateStatusCardBlade($id)
    {
        $task = Task::find($id);
        //Gate::authorize('update', $task); // Ditambahkan
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }
        $task->update([
            'status' => Task::STATUS_COMPLETED,
        ]);

        return redirect()->route('tasks.progress');
    }

    public function home()
    {
        $tasks = Task::where('user_id', auth()->id())->get();

        $completed_count = $tasks
            ->where('status', Task::STATUS_COMPLETED)
            ->count();

        $uncompleted_count = $tasks
            ->whereNotIn('status', Task::STATUS_COMPLETED)
            ->count();

        // return response()->json([
        //     'completed_count' => $completed_count,
        //      'uncompleted_count' => $uncompleted_count,
        // ], Response::HTTP_OK);

        return view('home', [
            'completed_count' => $completed_count,
            'uncompleted_count' => $uncompleted_count,
        ]);
    }
}
