<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Http\Requests\TodoRequest;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        $todos = auth()->user()->todos()->latest()->get();
        return view('todos.index', compact('todos'));
    }

    public function create()
    {
        return view('todos.create');
    }

    public function store(TodoRequest $request)
    {
        auth()->user()->todos()->create($request->validated());
        
        return redirect()->route('todos.index')
            ->with('success', 'Todo created successfully!');
    }

    public function show(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('todos.show', compact('todo'));
    }

    public function edit(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('todos.edit', compact('todo'));
    }

    public function update(TodoRequest $request, Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $todo->update($request->validated());

        return redirect()->route('todos.index')
            ->with('success', 'Todo updated successfully!');
    }

    public function destroy(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $todo->delete();

        return redirect()->route('todos.index')
            ->with('success', 'Todo deleted successfully!');
    }

    public function toggleComplete(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $todo->update([
            'is_completed' => !$todo->is_completed,
            'completed_at' => !$todo->is_completed ? now() : null
        ]);

        return redirect()->route('todos.index')
            ->with('success', 'Todo status updated!');
    }
}
