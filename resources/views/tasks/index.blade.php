@extends('layouts.master')
@section('pageTitle', $pageTitle)
@section('main')
<div class="task-list-container">
    <h1 class="task-list-heading">Task List</h1>

    <div class="task-list-task-buttons">
        <a href="{{ route('tasks.create') }}">
            <button class="task-list-button">
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
            <a href="{{ route('tasks.updateStatusFromIndex', ['id' => $task->id]) }}"
                class="material-icons @if ($task->status == 'completed') check-icon-completed @else check-icon @endif">
                check_circle
            </a>
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
        <div class="table-body-links">
            @can('update', $task)
            <a href="{{ route('tasks.edit', ['id' => $task->id]) }}">Edit</a>
            @endcan
            @can('delete', $task)
            <a href="{{ route('tasks.delete', ['id' => $task->id]) }}">Delete</a>
            @endcan
        </div>
    </div>
    @endforeach
</div>
@endsection