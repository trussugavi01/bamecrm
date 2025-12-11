# 🚀 Workflow Automation - Quick Reference Card

## ⚡ Essential Commands

### Start Queue Worker (Required!)
```bash
php artisan queue:work --tries=3
```
**Keep this running in a separate terminal!**

### Test Scheduled Jobs
```bash
# Run all scheduled jobs now
php artisan schedule:run

# View scheduled jobs
php artisan schedule:list

# Test specific job
php artisan tinker
>>> dispatch(new App\Jobs\CheckStagnantOpportunities);
```

### Clear Cache
```bash
php artisan optimize:clear
```

### View Logs
```bash
Get-Content storage/logs/laravel.log -Tail 50
```

---

## 🎯 Automated Behaviors

### When You Change Stage:
| Action | Automatic Result |
|--------|-----------------|
| Change to any stage | ✅ Probability auto-updates |
| Change to any stage | ✅ `stage_entry_date` set to today |
| Change to any stage | ✅ `days_in_stage` reset to 0 |
| Change to "Initial Outreach" | ✅ Task created: "Send introduction email" |
| Change to "Negotiation" | ✅ Notification sent to sales team |
| Change to "Contract & Commitment" | ✅ Task created: "Review contract" |
| Change to "Contract & Commitment" | ✅ Notification sent to leadership |
| Change to "Active Partnership" | ✅ `actual_close_date` set |
| Change to "Active Partnership" | ✅ Task created: "Send welcome package" |
| Change to "Active Partnership" | ✅ Won notification sent 🎉 |
| Change to "Closed Lost" | ✅ `actual_close_date` set |

### Daily Automated Tasks (08:00):
- 📧 Stagnant opportunity alerts (no activity in 14+ days)
- 📧 Follow-up reminders (for tomorrow's follow-ups)
- 📧 Overdue tasks digest

### Nightly Automated Tasks (02:00):
- 🔄 Update `days_in_stage` for all opportunities

### Morning Automated Tasks (09:00):
- ✅ Create follow-up tasks for proposals sent 3+ days ago

---

## 📊 Stage-Probability Mapping

| Stage | Probability |
|-------|------------|
| Prospect Identification | 10% |
| Initial Outreach | 20% |
| Qualification & Discovery | 35% |
| Proposal Development | 50% |
| Negotiation | 70% |
| Contract & Commitment | 90% |
| Active Partnership | 100% |
| Closed Lost | 0% |

---

## ✅ Validation Rules

### Cannot Move to "Contract & Commitment" Unless:
- ✅ `proposal_sent_date` is set

### Cannot Mark "Closed Lost" Unless:
- ✅ `loss_reason` is provided

### Cannot Move to "Active Partnership" Unless:
- ✅ `contract_signed_date` is set

---

## 🔔 Notification Recipients

| Event | Recipients |
|-------|-----------|
| Stagnant Deal | Opportunity owner |
| Follow-up Reminder | Opportunity owner |
| Stage → Negotiation | Sales team + Admins |
| Stage → Contract & Commitment | Leadership + Finance |
| Deal Won | Leadership + Finance + Owner |
| Overdue Tasks | Task owner |

---

## 📝 Activity Logging

### Log Activities Programmatically:
```php
$activityService = app(App\Services\ActivityService::class);

// Log a call
$activityService->logCall($sponsorshipId, 'Discussed pricing', 30);

// Log an email
$activityService->logEmail($sponsorshipId, 'Proposal sent', 'sent');

// Log a meeting
$activityService->logMeeting($sponsorshipId, 'Discovery call');

// Log a note
$activityService->logNote($sponsorshipId, 'Client very interested');
```

**Result:** Automatically updates `last_activity_date`

---

## 🐛 Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| Probability not updating | `php artisan optimize:clear` |
| Tasks not creating | Check queue worker is running |
| Notifications not sending | Check `.env` mail config |
| Jobs not running | Verify cron is set up |
| Observer not firing | Clear cache, restart server |

---

## 📦 Files You Can Customize

### Notification Templates:
- `app/Notifications/StagnantOpportunityNotification.php`
- `app/Notifications/FollowUpReminderNotification.php`
- `app/Notifications/StageProgressionNotification.php`
- `app/Notifications/WonOpportunityNotification.php`
- `app/Notifications/OverdueTasksDigestNotification.php`

### Task Creation Logic:
- `app/Services/WorkflowService.php`

### Scheduled Job Timing:
- `routes/console.php`

### Validation Rules:
- `app/Models/Sponsorship.php` → `canMoveToStage()` method

---

## 🎯 Testing Checklist

- [ ] Queue worker is running
- [ ] Create test sponsorship
- [ ] Change stage → verify probability updates
- [ ] Change stage → verify task created
- [ ] Check email inbox for notifications
- [ ] View `notifications` table in database
- [ ] Check `sponsorship_logs` for audit trail
- [ ] Log activity → verify `last_activity_date` updates
- [ ] Run scheduled jobs manually
- [ ] View Laravel logs for any errors

---

## 📞 Emergency Commands

### Stop Everything:
```bash
# Stop queue worker: Ctrl+C in terminal

# Clear all queued jobs
php artisan queue:clear

# Clear failed jobs
php artisan queue:flush
```

### Restart Everything:
```bash
php artisan optimize:clear
php artisan queue:restart
php artisan queue:work --tries=3
```

---

## 💡 Pro Tips

1. **Always keep queue worker running** during business hours
2. **Check logs daily** for first week after deployment
3. **Test with sample data** before using with real deals
4. **Customize notification timing** based on team preferences
5. **Monitor failed jobs** and retry if needed
6. **Use `php artisan tinker`** for quick testing
7. **Set up Supervisor** (Linux) for production queue management
8. **Configure Redis** for better queue performance in production

---

## 📚 Full Documentation

- **Complete Guide:** `WORKFLOW_AUTOMATION_GUIDE.md`
- **Setup Instructions:** `WORKFLOW_SETUP.md`
- **Implementation Summary:** `WORKFLOW_IMPLEMENTATION_SUMMARY.md`

---

**Need Help?** Check the full documentation files above or review Laravel logs.

**System Status:** ✅ Production Ready

*Last Updated: December 10, 2025*
