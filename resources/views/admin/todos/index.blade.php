<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin - All Todos by User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Statistics Cards (Clickable Filters) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <a href="{{ route('admin.todos.index') }}" class="bg-blue-100 p-4 rounded-lg shadow hover:bg-blue-200 transition cursor-pointer">
                    <p class="text-sm text-blue-600 font-medium">Total Users</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-blue-500 mt-1">Click to show all</p>
                </a>
                <a href="{{ route('admin.todos.index') }}" class="bg-purple-100 p-4 rounded-lg shadow hover:bg-purple-200 transition cursor-pointer">
                    <p class="text-sm text-purple-600 font-medium">Total Todos</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $stats['total_todos'] }}</p>
                    <p class="text-xs text-purple-500 mt-1">Click to show all</p>
                </a>
                <a href="{{ route('admin.todos.index', ['status' => 'completed']) }}" class="bg-green-100 p-4 rounded-lg shadow hover:bg-green-200 transition cursor-pointer">
                    <p class="text-sm text-green-600 font-medium">Completed</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['completed_todos'] }}</p>
                    <p class="text-xs text-green-500 mt-1">Click to filter</p>
                </a>
                <a href="{{ route('admin.todos.index', ['status' => 'pending']) }}" class="bg-yellow-100 p-4 rounded-lg shadow hover:bg-yellow-200 transition cursor-pointer">
                    <p class="text-sm text-yellow-600 font-medium">Pending</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending_todos'] }}</p>
                    <p class="text-xs text-yellow-500 mt-1">Click to filter</p>
                </a>
            </div>

            <!-- Filter Form -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <form method="GET" action="{{ route('admin.todos.index') }}" class="flex flex-wrap gap-4 items-end">
                    <!-- Search Input -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Name or email..." 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Role Filter -->
                    <div class="min-w-[150px]">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" 
                                id="role"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="min-w-[150px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Todo Status</label>
                        <select name="status" 
                                id="status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Todos</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.todos.index') }}" 
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Active Filters Display -->
            @if(request()->hasAny(['search', 'role', 'status']))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-medium text-blue-900">Active Filters:</span>
                            @if(request('search'))
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    Search: "{{ request('search') }}"
                                </span>
                            @endif
                            @if(request('role'))
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    Role: {{ ucfirst(request('role')) }}
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                    Status: {{ ucfirst(request('status')) }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('admin.todos.index') }}" 
                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Clear All
                        </a>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">
                        All Users and Their Todos
                    </h3>

                    @forelse($users as $user)
                        @if($user->todos->count() > 0 || !request()->filled('status'))
                            <div class="mb-8 border rounded-lg p-4 bg-gray-50">
                                <!-- User Header -->
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800">
                                            {{ $user->name }}
                                        </h4>
                                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Role: <span class="font-medium">{{ ucfirst($user->role) }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm text-gray-600">
                                            Total Todos: <span class="font-bold">{{ $user->todos->count() }}</span>
                                        </span>
                                        <br>
                                        <span class="text-sm text-green-600">
                                            Completed: <span class="font-bold">{{ $user->todos->where('is_completed', true)->count() }}</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- User's Todos -->
                                @if($user->todos->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($user->todos as $todo)
                                            <div class="bg-white p-4 rounded border {{ $todo->is_completed ? 'border-green-200' : 'border-gray-200' }}">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-3">
                                                            <h5 class="font-medium text-gray-900 {{ $todo->is_completed ? 'line-through text-gray-500' : '' }}">
                                                                {{ $todo->title }}
                                                            </h5>
                                                            @if($todo->is_completed)
                                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                                    Completed
                                                                </span>
                                                            @else
                                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                    Pending
                                                                </span>
                                                            @endif
                                                        </div>
                                                        
                                                        @if($todo->description)
                                                            <p class="text-sm text-gray-600 mt-2">
                                                                {{ $todo->description }}
                                                            </p>
                                                        @endif
                                                        
                                                        <p class="text-xs text-gray-400 mt-2">
                                                            Created: {{ $todo->created_at->format('M d, Y h:i A') }}
                                                        </p>
                                                        
                                                        @if($todo->due_date)
                                                            <p class="text-xs text-gray-500 mt-1">
                                                                Due: {{ \Carbon\Carbon::parse($todo->due_date)->format('M d, Y') }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <!-- Delete Button -->
                                                    <form action="{{ route('admin.todos.destroy', $todo) }}" method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to delete this todo?');"
                                                          class="ml-4">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900 text-sm font-medium px-3 py-1 border border-red-300 rounded hover:bg-red-50">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm italic">No todos matching the filter.</p>
                                @endif
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            No users found matching your filters.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>