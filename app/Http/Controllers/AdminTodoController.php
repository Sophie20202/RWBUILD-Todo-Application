<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTodoController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['todos' => function ($q) {
            $q->latest();
        }]);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->get();

        if ($request->filled('status')) {
            $users = $users->map(function($user) use ($request) {
                $user->todos = $user->todos->filter(function($todo) use ($request) {
                    if ($request->status === 'completed') {
                        return $todo->is_completed === true;
                    } elseif ($request->status === 'pending') {
                        return $todo->is_completed === false;
                    }
                    return true;
                });
                return $user;
            });
        }

        $stats = [
            'total_users' => $users->count(),
            'total_todos' => $users->sum(fn($user) => $user->todos->count()),
            'completed_todos' => $users->sum(fn($user) => $user->todos->where('is_completed', true)->count()),
            'pending_todos' => $users->sum(fn($user) => $user->todos->where('is_completed', false)->count()),
        ];

        return view('admin.todos.index', compact('users', 'stats'));
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();

        return redirect()->route('admin.todos.index')
            ->with('success', 'Admin action: Todo deleted successfully!');
    }
}