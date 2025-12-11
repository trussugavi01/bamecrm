<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\CheckStagnantOpportunities;
use App\Jobs\SendFollowUpReminders;
use App\Jobs\SendOverdueTasksDigest;
use App\Jobs\UpdateDaysInStage;
use App\Jobs\CheckProposalFollowUps;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automated tasks
Schedule::command('backup:database')->daily()->at('02:00');
Schedule::command('logs:clear --days=30')->weekly();
Schedule::command('queue:prune-batches')->daily();
Schedule::command('queue:prune-failed --hours=168')->daily();

// Workflow & Automation Scheduled Jobs
// 6.1 Stagnant Opportunities Checker - runs daily at 08:00
Schedule::job(new CheckStagnantOpportunities)->daily()->at('08:00')->name('check-stagnant-opportunities');

// 6.2 Follow-up Reminder Runner - runs daily at 08:00
Schedule::job(new SendFollowUpReminders)->daily()->at('08:00')->name('send-followup-reminders');

// 6.3 Overdue Tasks Digest - runs daily at 08:00
Schedule::job(new SendOverdueTasksDigest)->daily()->at('08:00')->name('send-overdue-tasks-digest');

// 6.4 Days in Stage Updater - runs nightly at 02:00
Schedule::job(new UpdateDaysInStage)->daily()->at('02:00')->name('update-days-in-stage');

// 6.5 Proposal Follow-up Trigger - runs daily at 09:00
Schedule::job(new CheckProposalFollowUps)->daily()->at('09:00')->name('check-proposal-followups');
