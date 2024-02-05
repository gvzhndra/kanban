<div class="task-progress-column">
    <div class="task-progress-column-heading">
        <h2>{{ $title }}</h2>
        <div class="task-list-task-buttons">
            <a href="{{ route('tasks.create') }}">
                <button  class="task-list-button" >
                    <span class="material-icons" style="margin-left: 5px;">add</span>
                </button>
            </a>
        </div>
    </div>
    <div>
        @foreach ($tasks as $task)
            @include('partials.task_card', [
              'task' => $task,
              'leftStatus' => $leftStatus,
              'rightStatus' => $rightStatus,
            ])
        @endforeach
    </div>
</div>
