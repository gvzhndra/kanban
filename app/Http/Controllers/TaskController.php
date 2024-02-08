<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $pageTitle = 'Task List'; // Ditambahkan
        $tasks = Task::all();;
        return view('tasks.index', [
            'pageTitle' => $pageTitle, //Ditambahkan
            'tasks' => $tasks,
        ]);
    }

    public function remaining_task()
    {
        $completedCount = Task::where('status', 'completed')->count();
        $remainingCount = Task::where('status', '!=', 'completed')->count();
        return view('home', compact('completedCount', 'remainingCount'));
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Task';
        $task = Task::find($id); // Diperbarui


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
            ],
            $request->all()
        );

        Task::create([
            'name' => $request->name,
            'detail' => $request->detail,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'due_date' => 'required',
            'status' => 'required',
        ]);

        $task = Task::find($id);
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
        $task = Task::find($id);
        return view('tasks.delete', ['pageTitle' => $pageTitle, 'task' => $task]);
    }

    public function destroy($id)
    {
        $task = Task::find($id);// Memperoleh task tertentu menggunakan $id
        $task->delete();
        // Melakukan redirect menuju tasks.index
        return redirect()->route('tasks.index');
    }

    public function progress()
    {
        $title = 'Task Progress';

        $allTasks = Task::all();

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

        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.progress');
    }

    public function completed(int $id, Request $request)
    {
        $task = Task::findOrFail($id);

        $task->update([
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.index');
    }


}
