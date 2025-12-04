<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Todo Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="flex justify-between items-start mb-4">
                            <h1 class="text-2xl font-bold {{ $todo->is_completed ? 'line-through text-gray-500' : '' }}">
                                {{ $todo->title }}
                            </h1>
                            
                            @if($todo->is_completed)
                                <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded">
                                    Completed
                                </span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded">
                                    Pending
                                </span>
                            @endif
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $todo->description ?? 'No description provided' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $todo->due_date->format('F d, Y') }}
                                        @if($todo->due_date->isPast() && !$todo->is_completed)
                                            <span class="ml-2 text-red-600 font-semibold">(Overdue)</span>
                                        @endif
                                    </dd>
                                </div>

                                @if($todo->is_completed && $todo->completed_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Completed At</dt>
                                        <dd class="mt-1 text-sm text-green-600 font-medium">
                                            {{ $todo->completed_at->format('F d, Y \a\t h:i A') }}
                                        </dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $todo->created_at->format('F d, Y \a\t h:i A') }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $todo->updated_at->format('F d, Y \a\t h:i A') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="flex space-x-3 border-t border-gray-200 pt-4">
                        <form action="{{ route('todos.toggle', $todo) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                class="bg-{{ $todo->is_completed ? 'yellow' : 'green' }}-500 hover:bg-{{ $todo->is_completed ? 'yellow' : 'green' }}-700 text-white font-bold py-2 px-4 rounded">
                                {{ $todo->is_completed ? 'Mark as Incomplete' : 'Mark as Complete' }}
                            </button>
                        </form>
                        
                        <a href="{{ route('todos.edit', $todo) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Todo
                        </a>
                        
                        <form action="{{ route('todos.destroy', $todo) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this todo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Delete
                            </button>
                        </form>

                        <a href="{{ route('todos.index') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>