<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $pageTitle = 'Task List'; // Ditambahkan
        $tasks = Task::all();;
        if (Gate::allows('viewAnyTask', Task::class)) {
            $tasks = Task::all();
        } else {
            $tasks = Task::where('user_id', Auth::user()->id)->get();
        }
//        dd($tasks);

        return view('tasks.index', [
            'pageTitle' => $pageTitle, //Ditambahkan
            'tasks' => $tasks,
        ]);
    }

//    public function remaining_task()
//    {
//        $completedCount = Task::where('status', 'completed')->count();
//        $remainingCount = Task::where('status', '!=', 'completed')->count();
//        return view('home', compact('completedCount', 'remainingCount'));
//    }

    public function home()
    {
        $tasks = Task::where('user_id', auth()->id())->get();

        $completed_count = $tasks
            ->where('status', Task::STATUS_COMPLETED)
            ->count();

        $uncompleted_count = $tasks
            ->whereNotIn('status', Task::STATUS_COMPLETED)
            ->count();

        return view('home', [
            'completed_count' => $completed_count,
            'uncompleted_count' => $uncompleted_count,
        ]);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Task';
        $task = Task::findOrFail($id); // Diperbarui

//        Gate::authorize('update', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }

        return view('tasks.edit', ['pageTitle' => $pageTitle, 'task' => $task]);
    }

    public function create()
    {
        $pageTitle = 'Create Task'; // Ditambahkan
        return view('tasks.create', [
            'pageTitle' => $pageTitle, //Ditambahkan
        ]);
    }
    // Tambahkan method store()
    public function store(Request $request)
    {
        // Code untuk proses validasi
        $request->validate(
            [
                'name' => 'required',
                'due_date' => 'required',
                'status' => 'required',
                'file' => ['max:5000', 'mimes:pdf,jpeg,png'],
            ],
            // Tambahkan pesan error
            [
                'file.max' => 'The file size exceed 5 mb',
                'file.mimes' => 'Must be a file of type: pdf,jpeg,png',
            ],
            $request->all()
        );


        // Tambahkan database transaction
        DB::beginTransaction();
        try {
            $task = Task::create([
                'name' => $request->name,
                'detail' => $request->detail,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'user_id' => Auth::user()->id,
                'file' => $request->file
            ]);

            $file = $request->file('file');
            if ($file) {
                $filename = $file->getClientOriginalName();
                $path = $file->storePubliclyAs(
                    'tasks',
                    $file->hashName(),
                    'public'
                );

                TaskFile::create([
                    'task_id' => $task->id,
                    'filename' => $filename,
                    'path' => $path,
                ]);
            }

            DB::commit();

            return redirect()->route('tasks.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('tasks.create')
                ->with('error', $th->getMessage());
        }

        return redirect()->route('tasks.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'due_date' => 'required',
            'status' => 'required',
        ]);

        $task = Task::findOrFail($id);
//        Gate::authorize('update', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }
        $task->update([
            // data task yang berasal dari formulir
            'name' => $request->name,
            'detail' => $request->detail,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);

        // Code untuk melakukan redirect menuju GET /tasks
        return redirect()->route('tasks.index');
    }

    public function delete($id){
        $pageTitle = 'Delete Task';
        $task = Task::findOrFail($id);
//        Gate::authorize('delete', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('deleteAnyTask', Task::class);
        }
        return view('tasks.delete', ['pageTitle' => $pageTitle, 'task' => $task]);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);// Memperoleh task tertentu menggunakan $id
//        Gate::authorize('delete', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('deleteAnyTask', Task::class);
        }
        $task->delete();
        // Melakukan redirect menuju tasks.index
        return redirect()->route('tasks.index');
    }

    public function progress()
    {
        $title = 'Task Progress';

//        $allTasks = Task::all();

        if (Gate::allows('viewAnyTask', Task::class)) {
            $allTasks = Task::all();
        } else {
            $allTasks = Task::where('user_id', Auth::user()->id)->get();
        }

        $filteredTasks = $allTasks->groupBy('status');

        $tasks = [
            Task::STATUS_NOT_STARTED => $filteredTasks->get(
                Task::STATUS_NOT_STARTED, []
            ),
            Task::STATUS_IN_PROGRESS => $filteredTasks->get(
                Task::STATUS_IN_PROGRESS, []
            ),
            Task::STATUS_IN_REVIEW => $filteredTasks->get(
                Task::STATUS_IN_REVIEW, []
            ),
            Task::STATUS_COMPLETED => $filteredTasks->get(
                Task::STATUS_COMPLETED, []
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

//        Gate::authorize('move', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }

        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.progress');
    }

    public function completed(int $id, Request $request)
    {
        $task = Task::findOrFail($id);
//        Gate::authorize('completed', $task);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }

        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.index');
    }


}
