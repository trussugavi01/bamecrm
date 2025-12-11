<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sponsorship;
use App\Models\User;

class LeadController extends Controller
{
    /**
     * Ingest external leads via API
     * 
     * POST /api/leads/ingest
     * Headers: X-API-KEY
     */
    public function ingest(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'source' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'tier' => 'nullable|in:Platinum,Gold,Silver,Bronze,In-Kind',
            'value' => 'nullable|numeric|min:0',
        ]);

        // Check if lead already exists
        $existingSponsorship = Sponsorship::where('decision_maker_email', $validated['email'])->first();

        if ($existingSponsorship) {
            // Append to existing record
            $existingSponsorship->notes = ($existingSponsorship->notes ? $existingSponsorship->notes . "\n\n" : '') 
                . "API Lead Submission (" . now()->format('Y-m-d H:i') . "):\n" 
                . json_encode($validated, JSON_PRETTY_PRINT);
            $existingSponsorship->save();
            
            $existingSponsorship->logActivity('form_submission', 'API lead submission received');

            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully',
                'data' => [
                    'sponsorship_id' => $existingSponsorship->id,
                    'status' => 'updated'
                ]
            ], 200);
        }

        // Create new sponsorship
        $admin = User::where('role', 'admin')->first();

        $sponsorship = Sponsorship::create([
            'user_id' => $admin->id ?? 1,
            'company_name' => $validated['company_name'],
            'decision_maker_name' => $validated['contact_name'] ?? null,
            'decision_maker_email' => $validated['email'],
            'tier' => $validated['tier'] ?? 'Bronze',
            'value' => $validated['value'] ?? 0,
            'stage' => 'Prospect Identification',
            'priority' => 'Warm',
            'source' => $validated['source'] ?? 'API',
            'notes' => isset($validated['message']) ? "Initial message:\n" . $validated['message'] : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => [
                'sponsorship_id' => $sponsorship->id,
                'status' => 'created',
                'stage' => $sponsorship->stage,
                'probability' => $sponsorship->probability
            ]
        ], 201);
    }
}
