<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeadForm;
use App\Models\Sponsorship;
use App\Models\User;

class PublicFormController extends Controller
{
    public function show($uuid)
    {
        $form = LeadForm::where('uuid', $uuid)->firstOrFail();
        
        return view('public.form', compact('form'));
    }

    public function submit(Request $request, $uuid)
    {
        $form = LeadForm::where('uuid', $uuid)->firstOrFail();
        
        // Build validation rules based on form schema
        $rules = [];
        foreach ($form->form_schema as $field => $config) {
            if ($config['visible']) {
                $fieldRules = [];
                if ($config['required']) {
                    $fieldRules[] = 'required';
                }
                
                if ($field === 'email') {
                    $fieldRules[] = 'email';
                }
                
                if (!empty($fieldRules)) {
                    $rules[$field] = implode('|', $fieldRules);
                }
            }
        }
        
        // Honeypot check
        if ($request->filled('website')) {
            // Spam detected
            return redirect()->back()->with('error', 'Invalid submission.');
        }
        
        $validated = $request->validate($rules);
        
        // Check if email already exists
        $existingSponsorship = Sponsorship::where('decision_maker_email', $validated['email'] ?? null)->first();
        
        if ($existingSponsorship) {
            // Append to existing record
            $existingSponsorship->notes = ($existingSponsorship->notes ? $existingSponsorship->notes . "\n\n" : '') 
                . "New Form Submission (" . now()->format('Y-m-d H:i') . "):\n" 
                . json_encode($validated, JSON_PRETTY_PRINT);
            $existingSponsorship->save();
            
            $existingSponsorship->logActivity('form_submission', 'New form submission received');
        } else {
            // Create new sponsorship record
            $admin = User::where('role', 'admin')->first();
            
            Sponsorship::create([
                'user_id' => $admin->id ?? $form->user_id,
                'company_name' => $validated['company'] ?? 'Unknown Company',
                'decision_maker_name' => $validated['name'] ?? null,
                'decision_maker_email' => $validated['email'] ?? null,
                'tier' => $validated['tier'] ?? 'Bronze',
                'value' => 0,
                'stage' => 'Prospect Identification',
                'priority' => 'Warm',
                'source' => 'Web Form',
                'notes' => isset($validated['message']) ? "Initial message:\n" . $validated['message'] : null,
            ]);
        }
        
        // Redirect or show success message
        if ($form->redirect_url) {
            return redirect($form->redirect_url);
        }
        
        return redirect()->back()->with('success', $form->success_message);
    }
}
