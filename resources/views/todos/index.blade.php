<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Todos') }}
            </h2>
            <a href="{{ route('todos.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Todo
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($todos->count() > 0)
                        <div class="space-y-4">
                            @foreach($todos as $todo)
                                <div class="border rounded-lg p-4 {{ $todo->is_completed ? 'bg-gray-50 border-green-300' : 'bg-white' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <!-- Title with completion status -->
                                            <div class="flex items-center gap-3 mb-2">
                                                @if($todo->is_completed)
                                                    <span class="text-green-600 text-xl">✓</span>
                                                @else
                                                    <span class="text-gray-400 text-xl">○</span>
                                                @endif
                                                <h3 class="text-lg font-semibold {{ $todo->is_completed ? 'text-green-600' : 'text-gray-900' }}">
                                                    {{ $todo->title }}
                                                </h3>
                                                @if($todo->is_completed)
                                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                                        Completed
                                                    </span>
                                                @else
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if($todo->description)
                                                <p class="text-gray-600 mt-2 ml-8">{{ $todo->description }}</p>
                                            @endif
                                            
                                            <div class="mt-3 ml-8 text-sm">
                                                <span class="text-gray-600">
                                                    <strong>Due:</strong> {{ $todo->due_date->format('M d, Y') }}
                                                </span>
                                                
                                                @if($todo->is_completed && $todo->completed_at)
                                                    <span class="ml-4 text-green-600 font-medium">
                                                        <strong>Completed:</strong> {{ $todo->completed_at->format('M d, Y h:i A') }}
                                                    </span>
                                                @endif

                                                @if(!$todo->is_completed && $todo->due_date->isPast())
                                                    <span class="ml-4 text-red-600 font-semibold">
                                                        (Overdue)
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex flex-wrap gap-2 ml-4">
                                            <!-- Toggle Complete Button -->
                                            <form action="{{ route('todos.toggle', $todo) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                    class="{{ $todo->is_completed ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white font-bold py-2 px-4 rounded text-sm transition">
                                                    {{ $todo->is_completed ? 'Mark Incomplete' : 'Mark Complete' }}
                                                </button>
                                            </form>
                                            
                                            <!-- View Button -->
                                            <a href="{{ route('todos.show', $todo) }}" 
                                               class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded text-sm transition">
                                                View
                                            </a>
                                            
                                            <!-- Edit Button -->
                                            <a href="{{ route('todos.edit', $todo) }}" 
                                               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-sm transition">
                                                Edit
                                            </a>
                                            
                                            <!-- Delete Button -->
                                            <form action="{{ route('todos.destroy', $todo) }}" method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this todo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-sm transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-lg mb-4">No todos yet. Create your first one!</p>
                            <a href="{{ route('todos.create') }}" 
                               class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create New Todo
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>