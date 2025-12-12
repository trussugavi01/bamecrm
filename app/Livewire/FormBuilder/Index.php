<?php

namespace App\Livewire\FormBuilder;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\LeadForm;

#[Layout('layouts.app')]
#[Title('Form Builder - B.A.M.E CRM')]
class Index extends Component
{
    public $forms;
    public $showCreateModal = false;
    public $editingFormId = null;
    
    // Form fields
    public $name = '';
    public $submit_button_text = 'Submit Inquiry';
    public $success_message = 'Thank you for your interest! We will be in touch soon.';
    public $redirect_url = '';
    
    // Schema fields
    public $schema = [];

    public function mount()
    {
        $this->loadForms();
        $this->resetSchema();
    }

    public function loadForms()
    {
        $this->forms = LeadForm::where('user_id', auth()->id())
            ->orWhere(function($query) {
                $query->whereHas('user', function($q) {
                    $q->where('role', 'admin');
                });
            })
            ->latest()
            ->get();
    }

    public function resetSchema()
    {
        $this->schema = LeadForm::defaultSchema();
    }

    public function openCreateModal()
    {
        \Log::info('FormBuilder: openCreateModal called');
        $this->reset(['name', 'redirect_url', 'editingFormId']);
        $this->submit_button_text = 'Submit Inquiry';
        $this->success_message = 'Thank you for your interest! We will be in touch soon.';
        $this->resetSchema();
        $this->showCreateModal = true;
    }

    public function editForm($formId)
    {
        $form = LeadForm::findOrFail($formId);
        
        $this->editingFormId = $formId;
        $this->name = $form->name;
        $this->submit_button_text = $form->submit_button_text;
        $this->success_message = $form->success_message;
        $this->redirect_url = $form->redirect_url ?? '';
        $this->schema = $form->form_schema;
        $this->showCreateModal = true;
    }

    public function saveForm()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'submit_button_text' => 'required|string|max:255',
            'success_message' => 'required|string',
            'redirect_url' => 'nullable|url|max:500',
        ]);

        if ($this->editingFormId) {
            $form = LeadForm::findOrFail($this->editingFormId);
            $form->update([
                'name' => $this->name,
                'form_schema' => $this->schema,
                'submit_button_text' => $this->submit_button_text,
                'success_message' => $this->success_message,
                'redirect_url' => $this->redirect_url ?: null,
            ]);
            
            session()->flash('success', 'Form updated successfully!');
        } else {
            LeadForm::create([
                'user_id' => auth()->id(),
                'name' => $this->name,
                'form_schema' => $this->schema,
                'submit_button_text' => $this->submit_button_text,
                'success_message' => $this->success_message,
                'redirect_url' => $this->redirect_url ?: null,
            ]);
            
            session()->flash('success', 'Form created successfully!');
        }

        $this->showCreateModal = false;
        $this->loadForms();
    }

    public function deleteForm($formId)
    {
        LeadForm::findOrFail($formId)->delete();
        session()->flash('success', 'Form deleted successfully!');
        $this->loadForms();
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
    }

    public function render()
    {
        return view('livewire.form-builder.index');
    }
}
