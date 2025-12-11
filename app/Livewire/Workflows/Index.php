<?php

namespace App\Livewire\Workflows;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Workflow;
use App\Models\Task;
use App\Models\Notification;
use App\Models\User;
use App\Models\Sponsorship;

#[Layout('layouts.app')]
#[Title('Workflow Automation - B.A.M.E CRM')]
class Index extends Component
{
    public $activeTab = 'workflows';
    
    // Workflow form
    public $showWorkflowModal = false;
    public $editingWorkflowId = null;
    public $workflowName = '';
    public $workflowDescription = '';
    public $triggerType = '';
    public $triggerStage = '';
    public $actions = [];
    
    // Task form
    public $showTaskModal = false;
    public $editingTaskId = null;
    public $taskTitle = '';
    public $taskDescription = '';
    public $taskDueDate = '';
    public $taskPriority = 'medium';
    public $taskAssignedTo = '';
    public $taskSponsorshipId = '';

    public function mount()
    {
        $this->actions = [
            ['type' => '', 'config' => []]
        ];
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // ==================== WORKFLOWS ====================

    public function openWorkflowModal()
    {
        $this->reset(['editingWorkflowId', 'workflowName', 'workflowDescription', 'triggerType', 'triggerStage']);
        $this->actions = [['type' => '', 'config' => []]];
        $this->showWorkflowModal = true;
    }

    public function editWorkflow($id)
    {
        $workflow = Workflow::findOrFail($id);
        $this->editingWorkflowId = $id;
        $this->workflowName = $workflow->name;
        $this->workflowDescription = $workflow->description;
        $this->triggerType = $workflow->trigger_type;
        $this->triggerStage = $workflow->trigger_config['stage'] ?? '';
        $this->actions = $workflow->actions ?: [['type' => '', 'config' => []]];
        $this->showWorkflowModal = true;
    }

    public function addAction()
    {
        $this->actions[] = ['type' => '', 'config' => []];
    }

    public function removeAction($index)
    {
        unset($this->actions[$index]);
        $this->actions = array_values($this->actions);
    }

    public function saveWorkflow()
    {
        $this->validate([
            'workflowName' => 'required|string|max:255',
            'triggerType' => 'required|string',
        ]);

        $triggerConfig = [];
        if ($this->triggerType === Workflow::TRIGGER_STAGE_CHANGE && $this->triggerStage) {
            $triggerConfig['stage'] = $this->triggerStage;
        }

        // Filter out empty actions
        $validActions = array_filter($this->actions, fn($a) => !empty($a['type']));

        $data = [
            'user_id' => auth()->id(),
            'name' => $this->workflowName,
            'description' => $this->workflowDescription,
            'trigger_type' => $this->triggerType,
            'trigger_config' => $triggerConfig,
            'actions' => array_values($validActions),
        ];

        if ($this->editingWorkflowId) {
            Workflow::findOrFail($this->editingWorkflowId)->update($data);
            session()->flash('success', 'Workflow updated successfully!');
        } else {
            Workflow::create($data);
            session()->flash('success', 'Workflow created successfully!');
        }

        $this->showWorkflowModal = false;
    }

    public function toggleWorkflow($id)
    {
        $workflow = Workflow::findOrFail($id);
        $workflow->update(['is_active' => !$workflow->is_active]);
        session()->flash('success', $workflow->is_active ? 'Workflow activated!' : 'Workflow deactivated!');
    }

    public function deleteWorkflow($id)
    {
        Workflow::findOrFail($id)->delete();
        session()->flash('success', 'Workflow deleted successfully!');
    }

    // ==================== TASKS ====================

    public function openTaskModal()
    {
        $this->reset(['editingTaskId', 'taskTitle', 'taskDescription', 'taskDueDate', 'taskPriority', 'taskAssignedTo', 'taskSponsorshipId']);
        $this->taskDueDate = now()->addDays(3)->format('Y-m-d');
        $this->showTaskModal = true;
    }

    public function editTask($id)
    {
        $task = Task::findOrFail($id);
        $this->editingTaskId = $id;
        $this->taskTitle = $task->title;
        $this->taskDescription = $task->description;
        $this->taskDueDate = $task->due_date?->format('Y-m-d');
        $this->taskPriority = $task->priority;
        $this->taskAssignedTo = $task->assigned_to;
        $this->taskSponsorshipId = $task->sponsorship_id;
        $this->showTaskModal = true;
    }

    public function saveTask()
    {
        $this->validate([
            'taskTitle' => 'required|string|max:255',
            'taskDueDate' => 'nullable|date',
            'taskPriority' => 'required|in:low,medium,high',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'title' => $this->taskTitle,
            'description' => $this->taskDescription,
            'due_date' => $this->taskDueDate ?: null,
            'priority' => $this->taskPriority,
            'assigned_to' => $this->taskAssignedTo ?: null,
            'sponsorship_id' => $this->taskSponsorshipId ?: null,
        ];

        if ($this->editingTaskId) {
            Task::findOrFail($this->editingTaskId)->update($data);
            session()->flash('success', 'Task updated successfully!');
        } else {
            Task::create($data);
            session()->flash('success', 'Task created successfully!');
        }

        $this->showTaskModal = false;
    }

    public function completeTask($id)
    {
        $task = Task::findOrFail($id);
        $task->markAsCompleted();
        session()->flash('success', 'Task marked as completed!');
    }

    public function deleteTask($id)
    {
        Task::findOrFail($id)->delete();
        session()->flash('success', 'Task deleted successfully!');
    }

    // ==================== NOTIFICATIONS ====================

    public function markNotificationAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();
    }

    public function markAllNotificationsAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        session()->flash('success', 'All notifications marked as read!');
    }

    public function deleteNotification($id)
    {
        Notification::where('user_id', auth()->id())->findOrFail($id)->delete();
    }

    public function closeModal()
    {
        $this->showWorkflowModal = false;
        $this->showTaskModal = false;
    }

    public function render()
    {
        $workflows = Workflow::with('user')
            ->latest()
            ->get();

        $tasks = Task::with(['assignee', 'sponsorship'])
            ->where(function($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('assigned_to', auth()->id());
            })
            ->where('status', '!=', Task::STATUS_COMPLETED)
            ->orderBy('due_date')
            ->get();

        $completedTasks = Task::with(['assignee', 'sponsorship'])
            ->where(function($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('assigned_to', auth()->id());
            })
            ->where('status', Task::STATUS_COMPLETED)
            ->latest('completed_at')
            ->limit(10)
            ->get();

        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->limit(50)
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        $users = User::all();
        $sponsorships = Sponsorship::whereNotIn('stage', ['Closed Won', 'Closed Lost'])->get();

        return view('livewire.workflows.index', [
            'workflows' => $workflows,
            'tasks' => $tasks,
            'completedTasks' => $completedTasks,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'users' => $users,
            'sponsorships' => $sponsorships,
            'triggers' => Workflow::TRIGGERS,
            'actionTypes' => Workflow::ACTIONS_LIST,
            'stages' => Sponsorship::STAGES,
            'priorities' => Task::PRIORITIES,
        ]);
    }
}
