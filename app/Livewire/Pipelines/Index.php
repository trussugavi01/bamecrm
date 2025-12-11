<?php

namespace App\Livewire\Pipelines;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Pipeline;
use App\Models\PipelineStage;

#[Layout('layouts.app')]
#[Title('Pipeline Management - B.A.M.E CRM')]
class Index extends Component
{
    public $pipelines;
    public $showCreateModal = false;
    public $editingPipelineId = null;
    
    // Form fields
    public $name = '';
    public $description = '';
    public $is_default = false;
    public $stages = [];

    public function mount()
    {
        $this->loadPipelines();
    }

    public function loadPipelines()
    {
        $this->pipelines = Pipeline::with('stages', 'sponsorships')
            ->where('user_id', auth()->id())
            ->orWhere('is_default', true)
            ->latest()
            ->get();
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'description', 'is_default', 'editingPipelineId']);
        $this->stages = Pipeline::defaultStages();
        $this->showCreateModal = true;
    }

    public function editPipeline($pipelineId)
    {
        $pipeline = Pipeline::with('stages')->findOrFail($pipelineId);
        
        $this->editingPipelineId = $pipelineId;
        $this->name = $pipeline->name;
        $this->description = $pipeline->description ?? '';
        $this->is_default = $pipeline->is_default;
        $this->stages = $pipeline->stages->map(fn($stage) => [
            'name' => $stage->name,
            'probability' => $stage->probability,
            'order' => $stage->order,
            'color' => $stage->color,
        ])->toArray();
        
        $this->showCreateModal = true;
    }

    public function addStage()
    {
        $this->stages[] = [
            'name' => '',
            'probability' => 50,
            'order' => count($this->stages) + 1,
            'color' => '#6B7280',
        ];
    }

    public function removeStage($index)
    {
        unset($this->stages[$index]);
        $this->stages = array_values($this->stages);
        
        // Reorder
        foreach ($this->stages as $i => $stage) {
            $this->stages[$i]['order'] = $i + 1;
        }
    }

    public function savePipeline()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stages' => 'required|array|min:1',
            'stages.*.name' => 'required|string|max:255',
            'stages.*.probability' => 'required|integer|min:0|max:100',
            'stages.*.color' => 'required|string',
        ]);

        if ($this->editingPipelineId) {
            $pipeline = Pipeline::findOrFail($this->editingPipelineId);
            $pipeline->update([
                'name' => $this->name,
                'description' => $this->description,
                'is_default' => $this->is_default,
            ]);
            
            // Delete existing stages and recreate
            $pipeline->stages()->delete();
        } else {
            $pipeline = Pipeline::create([
                'user_id' => auth()->id(),
                'name' => $this->name,
                'description' => $this->description,
                'is_default' => $this->is_default,
            ]);
        }

        // Create stages
        foreach ($this->stages as $stageData) {
            PipelineStage::create([
                'pipeline_id' => $pipeline->id,
                'name' => $stageData['name'],
                'probability' => $stageData['probability'],
                'order' => $stageData['order'],
                'color' => $stageData['color'],
            ]);
        }

        session()->flash('success', $this->editingPipelineId ? 'Pipeline updated successfully!' : 'Pipeline created successfully!');
        
        $this->showCreateModal = false;
        $this->loadPipelines();
    }

    public function deletePipeline($pipelineId)
    {
        $pipeline = Pipeline::findOrFail($pipelineId);
        
        // Check if pipeline has sponsorships
        if ($pipeline->sponsorships()->count() > 0) {
            session()->flash('error', 'Cannot delete pipeline with active sponsorships.');
            return;
        }
        
        $pipeline->delete();
        session()->flash('success', 'Pipeline deleted successfully!');
        $this->loadPipelines();
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
    }

    public function render()
    {
        return view('livewire.pipelines.index');
    }
}
