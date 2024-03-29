@extends('layouts.master')

@section('pageTitle', $pageTitle)

@section('main')
    <div class="task-list-container">
        <h1 class="task-list-heading">Task List</h1>

        <div class="task-list-task-buttons">
            <a href="{{ route('tasks.create') }}">
                <button  class="task-list-button">
                    <span class="material-icons">add</span>Add task
                </button>
            </a>
        </div>

        <div class="task-list-table-head">
            <div class="task-list-header-task-name">Task Name</div>
            <div class="task-list-header-detail">Detail</div>
            <div class="task-list-header-due-date">Due Date</div>
            <div class="task-list-header-progress">Progress</div>
            <div class="task-list-header-file">Files</div>
            <div class="task-list-header-owner-name">Owner</div>
            <div class="task-list-header-links"></div>
        </div>

        @foreach ($tasks as $index => $task)
            <div class="table-body">
                <div class="table-body-task-name">
                    <span>
                    @if ($task->status == 'completed')
                        <div class="material-icons task-progress-card-top-checked" style="margin-right: 5px;">check_circle </div>
                    @else
                        <form action="{{ route('tasks.completed', ['id' => $task->id, 'status' => 'completed']) }}" method="POST" id="trigger-submit-{{$task->id}}">
                            @csrf
                            @method('patch')
                            <button type="submit" class="material-icons task-progress-card-top-check">check_circle </button>
                        </form>
                    @endif
                    </span>
                    {{ $task->name }}
                </div>
                <div class="table-body-detail"> {{ $task->detail }} </div>
                <div class="table-body-due-date"> {{ $task->due_date }} </div>
                <div class="table-body-progress">
                    @switch($task->status)
                        @case('in_progress')
                            In Progress
                            @break
                        @case('in_review')
                            Waiting/In Review
                            @break
                        @case('completed')
                            Completed
                            @break
                        @default
                            Not Started
                    @endswitch
                </div>
                <div class="table-body-file">
                    @foreach ($task->files as $file)
                        <a href="{{ route('tasks.files.show', ['task_id' => $task->id, 'id' => $file->id]) }}">
                            {{ $file->filename }}</a>
                    @endforeach
                </div>
                <div class="table-body-owner-name">{{ $task->user->name }}</div>
                <div>
                    @canany(['updateAnyTask', 'performAsTaskOwner'], $task)
                        <a href="{{ route('tasks.edit', ['id' => $task->id]) }}">Edit</a>
                    @endcan
                    @canany(['deleteAnyTask', 'performAsTaskOwner'], $task)
                        <a href="{{ route('tasks.delete', ['id' => $task->id]) }}">Delete</a>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>
@endsection
