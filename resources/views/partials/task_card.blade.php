<div class="task-progress-card">
    <div class="task-progress-card-left">
        @if ($task->status == 'completed')
            <div class="material-icons task-progress-card-top-checked">check_circle</div>
        @else
            <form
                action="{{ route('tasks.move', ['id' => $task->id, 'status' => 'completed']) }}" method="POST" id="trigger-submit-{{$task->id}}">
                @csrf
                @method('patch')
                <div class="material-icons task-progress-card-top-check" onclick="document.getElementById('trigger-submit-{{$task->id}}').submit()" >check_circle</div>
            </form>
        @endif
        <a href="{{ route('tasks.edit', ['id' => $task->id]) }}" class="material-icons task-progress-card-top-edit">more_vert</a>
    </div>
    <p class="task-progress-card-title">{{ $task->name }}</p>
    <div>
        <p>{{ $task->detail }}</p>
    </div>
    <div>
        <p>Due on {{ $task->due_date }}</p>
    </div>
    <div>
        <p>Owner: <strong>{{ $task->user->name }}</strong></p>
    </div>
    @if ($task->files)
        <div>
            @foreach ($task->files as $file)
                <div class="task-progress-card-file">
                    <span class="material-icons">file_open</span>
                    <a target="_blank"
                       href="{{ route('tasks.files.show', ['task_id' => $task->id, 'id' => $file->id]) }}">
                        {{ $file->filename }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @canany(['performAsTaskOwner'], $task)
    <div class="@if ($leftStatus) task-progress-card-left @else task-progress-card-right @endif">
        @if ($leftStatus)
            <!--Mengapit "button" dengan "form"-->
            <form
                action="{{ route('tasks.move', ['id' => $task->id, 'status' => $leftStatus]) }}"
                method="POST"
            >
                @method('patch')
                @csrf
                <button class="material-icons">chevron_left</button>
            </form>
        @endif

        @if ($rightStatus)
            <!-- Mengapit "button" dengan "form"-->
            <form
                action="{{ route('tasks.move', ['id' => $task->id, 'status' => $rightStatus]) }}"
                method="POST"
            >
                @method('patch')
                @csrf
                <button class="material-icons">chevron_right</button>
            </form>
        @endif
    </div>
    @endcan
</div>
