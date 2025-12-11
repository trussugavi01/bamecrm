<div>
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Workflow Automation</h2>
        <p class="text-sm text-gray-600">Automate routine tasks, notifications, and follow-up activities</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button 
                wire:click="setTab('workflows')"
                class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'workflows' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Workflows
            </button>
            <button 
                wire:click="setTab('tasks')"
                class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'tasks' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Tasks
                @if($tasks->count() > 0)
                    <span class="ml-2 bg-pink-100 text-pink-600 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $tasks->count() }}</span>
                @endif
            </button>
            <button 
                wire:click="setTab('notifications')"
                class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'notifications' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Notifications
                @if($unreadCount > 0)
                    <span class="ml-2 bg-red-100 text-red-600 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                @endif
            </button>
        </nav>
    </div>

    <!-- Workflows Tab -->
    @if($activeTab === 'workflows')
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Automation Rules</h3>
                <button wire:click="openWorkflowModal" class="bg-pink-500 hover:bg-pink-600 text-white font-semibold py-2 px-4 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Workflow
                </button>
            </div>

            @if($workflows->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workflow</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trigger</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Executions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($workflows as $workflow)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $workflow->name }}</div>
                                        @if($workflow->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($workflow->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $workflow->trigger_label }}
                                        </span>
                                        @if(isset($workflow->trigger_config['stage']))
                                            <div class="text-xs text-gray-500 mt-1">Stage: {{ $workflow->trigger_config['stage'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ count($workflow->actions ?? []) }} action(s)</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $workflow->execution_count }}</div>
                                        @if($workflow->last_executed_at)
                                            <div class="text-xs text-gray-500">Last: {{ $workflow->last_executed_at->diffForHumans() }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button 
                                            wire:click="toggleWorkflow({{ $workflow->id }})"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $workflow->is_active ? 'bg-green-500' : 'bg-gray-200' }}"
                                        >
                                            <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $workflow->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="editWorkflow({{ $workflow->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <button wire:click="deleteWorkflow({{ $workflow->id }})" wire:confirm="Are you sure you want to delete this workflow?" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <p class="text-gray-500 mb-4">No workflows created yet</p>
                    <button wire:click="openWorkflowModal" class="bg-pink-500 hover:bg-pink-600 text-white font-semibold py-2 px-4 rounded-lg">
                        Create Your First Workflow
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- Tasks Tab -->
    @if($activeTab === 'tasks')
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Follow-up Tasks</h3>
                <button wire:click="openTaskModal" class="bg-pink-500 hover:bg-pink-600 text-white font-semibold py-2 px-4 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Task
                </button>
            </div>

            <!-- Pending Tasks -->
            <div class="mb-8">
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Pending Tasks ({{ $tasks->count() }})</h4>
                @if($tasks->count() > 0)
                    <div class="space-y-3">
                        @foreach($tasks as $task)
                            <div class="bg-white rounded-lg shadow p-4 border-l-4 {{ $task->isOverdue() ? 'border-red-500' : ($task->isDueToday() ? 'border-yellow-500' : 'border-blue-500') }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3">
                                        <button 
                                            wire:click="completeTask({{ $task->id }})"
                                            class="mt-1 w-5 h-5 rounded border-2 border-gray-300 hover:border-green-500 hover:bg-green-50 flex items-center justify-center"
                                        >
                                            <svg class="w-3 h-3 text-transparent hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <div>
                                            <h5 class="font-medium text-gray-900">{{ $task->title }}</h5>
                                            @if($task->description)
                                                <p class="text-sm text-gray-500 mt-1">{{ $task->description }}</p>
                                            @endif
                                            <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                @if($task->due_date)
                                                    <span class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        {{ $task->due_date->format('M d, Y') }}
                                                        @if($task->isOverdue())
                                                            (Overdue)
                                                        @elseif($task->isDueToday())
                                                            (Today)
                                                        @endif
                                                    </span>
                                                @endif
                                                @if($task->sponsorship)
                                                    <span>
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                        {{ $task->sponsorship->company_name }}
                                                    </span>
                                                @endif
                                                @if($task->assignee)
                                                    <span>
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        {{ $task->assignee->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}
                                        ">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        <button wire:click="editTask({{ $task->id }})" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="deleteTask({{ $task->id }})" wire:confirm="Delete this task?" class="text-gray-400 hover:text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-white rounded-lg">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <p class="text-gray-500">No pending tasks</p>
                    </div>
                @endif
            </div>

            <!-- Completed Tasks -->
            @if($completedTasks->count() > 0)
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Recently Completed</h4>
                    <div class="space-y-2">
                        @foreach($completedTasks as $task)
                            <div class="bg-gray-50 rounded-lg p-3 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-500 line-through">{{ $task->title }}</span>
                                </div>
                                <span class="text-xs text-gray-400">{{ $task->completed_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Notifications Tab -->
    @if($activeTab === 'notifications')
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                @if($unreadCount > 0)
                    <button wire:click="markAllNotificationsAsRead" class="text-sm text-pink-600 hover:text-pink-700">
                        Mark all as read
                    </button>
                @endif
            </div>

            @if($notifications->count() > 0)
                <div class="space-y-3">
                    @foreach($notifications as $notification)
                        <div class="bg-white rounded-lg shadow p-4 {{ !$notification->is_read ? 'border-l-4 border-pink-500' : '' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                                        {{ $notification->type === 'success' ? 'bg-green-100' : '' }}
                                        {{ $notification->type === 'warning' ? 'bg-yellow-100' : '' }}
                                        {{ $notification->type === 'error' ? 'bg-red-100' : '' }}
                                        {{ $notification->type === 'info' ? 'bg-blue-100' : '' }}
                                    ">
                                        @if($notification->type === 'success')
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @elseif($notification->type === 'warning')
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        @elseif($notification->type === 'error')
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900">{{ $notification->title }}</h5>
                                        <p class="text-sm text-gray-500 mt-1">{{ $notification->message }}</p>
                                        <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if(!$notification->is_read)
                                        <button wire:click="markNotificationAsRead({{ $notification->id }})" class="text-gray-400 hover:text-gray-600" title="Mark as read">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                    <button wire:click="deleteNotification({{ $notification->id }})" class="text-gray-400 hover:text-red-600" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-gray-500">No notifications yet</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Workflow Modal -->
    @if($showWorkflowModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full">
                    <div class="flex items-center justify-between p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">{{ $editingWorkflowId ? 'Edit Workflow' : 'Create Workflow' }}</h2>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="saveWorkflow" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Workflow Name</label>
                                <input type="text" wire:model="workflowName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="e.g., Follow up on stagnant deals">
                                @error('workflowName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                <textarea wire:model="workflowDescription" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="Describe what this workflow does..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Trigger</label>
                                <select wire:model.live="triggerType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Select a trigger...</option>
                                    @foreach($triggers as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('triggerType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            @if($triggerType === 'stage_change')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">When deal moves to stage</label>
                                    <select wire:model="triggerStage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                        <option value="">Any stage</option>
                                        @foreach($stages as $stage)
                                            <option value="{{ $stage }}">{{ $stage }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Actions</label>
                                <div class="space-y-3">
                                    @foreach($actions as $index => $action)
                                        <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-lg">
                                            <select wire:model="actions.{{ $index }}.type" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                                <option value="">Select action...</option>
                                                @foreach($actionTypes as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @if(count($actions) > 1)
                                                <button type="button" wire:click="removeAction({{ $index }})" class="text-red-500 hover:text-red-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addAction" class="mt-2 text-sm text-pink-600 hover:text-pink-700">
                                    + Add another action
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg">
                                {{ $editingWorkflowId ? 'Update Workflow' : 'Create Workflow' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Task Modal -->
    @if($showTaskModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="closeModal"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full">
                    <div class="flex items-center justify-between p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-900">{{ $editingTaskId ? 'Edit Task' : 'Create Task' }}</h2>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="saveTask" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Task Title</label>
                                <input type="text" wire:model="taskTitle" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="e.g., Follow up with client">
                                @error('taskTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                <textarea wire:model="taskDescription" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="Add details..."></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                    <input type="date" wire:model="taskDueDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                    <select wire:model="taskPriority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                        @foreach($priorities as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assign To (Optional)</label>
                                <select wire:model="taskAssignedTo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Related Deal (Optional)</label>
                                <select wire:model="taskSponsorshipId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                    <option value="">No related deal</option>
                                    @foreach($sponsorships as $sponsorship)
                                        <option value="{{ $sponsorship->id }}">{{ $sponsorship->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg">
                                {{ $editingTaskId ? 'Update Task' : 'Create Task' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
