<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sponsorship;
use App\Models\User;

class SampleSponsorshipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@bamecrm.com')->first();
        $consultant = User::where('email', 'consultant@bamecrm.com')->first();

        $deals = [
            [
                'user_id' => $admin->id,
                'company_name' => 'Tech Solutions Inc.',
                'decision_maker_name' => 'Sarah Johnson',
                'decision_maker_email' => 'sarah@techsolutions.com',
                'tier' => 'Gold',
                'value' => 5000,
                'stage' => 'Prospect Identification',
                'priority' => 'Warm',
                'source' => 'Outreach',
            ],
            [
                'user_id' => $consultant->id,
                'company_name' => 'Innovate Pharma',
                'decision_maker_name' => 'Michael Chen',
                'decision_maker_email' => 'mchen@innovatepharma.com',
                'tier' => 'Platinum',
                'value' => 10000,
                'stage' => 'Qualification & Discovery',
                'priority' => 'Hot',
                'source' => 'Referral',
                'last_activity_at' => now()->subDays(20), // Stagnant
            ],
            [
                'user_id' => $admin->id,
                'company_name' => 'Global Health Corp',
                'decision_maker_name' => 'Emily Rodriguez',
                'decision_maker_email' => 'emily@globalhealth.com',
                'tier' => 'Silver',
                'value' => 2500,
                'stage' => 'Initial Outreach',
                'priority' => 'Warm',
                'source' => 'Event',
            ],
            [
                'user_id' => $consultant->id,
                'company_name' => 'Community Bank',
                'decision_maker_name' => 'David Thompson',
                'decision_maker_email' => 'dthompson@communitybank.com',
                'tier' => 'Platinum',
                'value' => 7500,
                'stage' => 'Proposal Development',
                'priority' => 'Warm',
                'source' => 'Web Form',
                'proposal_sent_date' => now()->subDays(5),
            ],
            [
                'user_id' => $admin->id,
                'company_name' => 'Creative Agency LLC',
                'decision_maker_name' => 'Jessica Martinez',
                'decision_maker_email' => 'jessica@creativeagency.com',
                'tier' => 'Platinum',
                'value' => 7500,
                'stage' => 'Negotiation',
                'priority' => 'Hot',
                'source' => 'Referral',
                'proposal_sent_date' => now()->subDays(15),
            ],
            [
                'user_id' => $consultant->id,
                'company_name' => 'Logistics Group',
                'decision_maker_name' => 'Robert Williams',
                'decision_maker_email' => 'rwilliams@logisticsgroup.com',
                'tier' => 'Platinum',
                'value' => 7500,
                'stage' => 'Qualification & Discovery',
                'priority' => 'Warm',
                'source' => 'Outreach',
            ],
            [
                'user_id' => $admin->id,
                'company_name' => 'Education Foundation',
                'decision_maker_name' => 'Amanda Brown',
                'decision_maker_email' => 'abrown@edufoundation.org',
                'tier' => 'Gold',
                'value' => 5000,
                'stage' => 'Negotiation',
                'priority' => 'Warm',
                'source' => 'Event',
                'proposal_sent_date' => now()->subDays(10),
            ],
        ];

        foreach ($deals as $deal) {
            Sponsorship::create($deal);
        }
    }
}
