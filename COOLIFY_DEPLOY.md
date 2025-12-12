# Coolify Deployment Guide for BAME CRM

This guide provides step-by-step instructions for deploying BAME CRM to Coolify on your Hostinger VPS.

## Prerequisites

- ✅ Coolify installed at https://apps.bamecrm.online
- ✅ GitHub repository with your code pushed
- ✅ Domain name configured (e.g., bamecrm.online)

## Deployment Steps

### 1. Generate Application Key

Before deploying, generate your Laravel application key locally:

```bash
php artisan key:generate --show
```

Copy the output (e.g., `base64:xxxxxxxxxxxxx`) - you'll need this for Coolify.

### 2. Create New Application in Coolify

1. Log into Coolify at https://apps.bamecrm.online
2. Click **"+ New Resource"** → **"Application"**
3. Select **"Public Repository"** (or connect your GitHub account for private repos)
4. Enter your repository details:
   - **Repository URL:** `https://github.com/yourusername/bamecrm`
   - **Branch:** `main` (or your default branch)
   - **Build Pack:** Nixpacks (should auto-detect)

### 3. Configure Build Settings

In the application settings:

**General Tab:**
- **Name:** BAME CRM
- **Description:** Customer Relationship Management System

**Build Tab:**
- **Build Pack:** Nixpacks (auto-detected from nixpacks.toml)
- **Base Directory:** `/` (root)
- **Publish Directory:** `public` (Laravel public folder)

### 4. Configure Processes

Enable all three processes in the **Processes** tab:

- ✅ **web** - Main application server
- ✅ **worker** - Queue worker for background jobs
- ✅ **scheduler** - Laravel task scheduler

### 5. Configure Persistent Storage

In the **Storage** tab, add these persistent volumes:

| Source Path | Destination Path | Description |
|------------|------------------|-------------|
| `/storage` | `/app/storage` | Application storage (logs, cache, uploads) |
| `/database` | `/app/database` | SQLite database file |
| `/bootstrap-cache` | `/app/bootstrap/cache` | Bootstrap cache |

### 6. Set Environment Variables

In the **Environment Variables** tab, add these variables:

#### Required Variables

```bash
APP_NAME="BAME CRM"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_URL=https://bamecrm.online
APP_TIMEZONE=UTC

# Database (SQLite)
DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite

# Cache & Session
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DAILY_DAYS=14

# Security
API_KEY_SALT=YOUR_SECURE_RANDOM_STRING_HERE
```

#### Email Configuration (Choose One)

**Option A: ZeptoMail (Recommended - 10,000 emails/month free)**
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.zeptomail.com
MAIL_PORT=587
MAIL_USERNAME=emailapikey
MAIL_PASSWORD=your-zeptomail-send-token
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bamecrm.online
MAIL_FROM_NAME="BAME CRM"
```

**Setup Steps for ZeptoMail:**
1. Sign up at https://www.zoho.com/zeptomail/
2. Verify your domain (add SPF, DKIM, CNAME records to your DNS)
3. Go to **Mail Agents** → **SMTP** → Create new SMTP user
4. Copy the **Send Token** (this is your MAIL_PASSWORD)
5. Username is always `emailapikey`
6. Verify sender address: noreply@bamecrm.online

**Option B: Mailgun**
```bash
MAIL_MAILER=mailgun
MAIL_FROM_ADDRESS=noreply@bamecrm.online
MAIL_FROM_NAME="BAME CRM"
MAILGUN_DOMAIN=mg.bamecrm.online
MAILGUN_SECRET=your-mailgun-api-key
MAILGUN_ENDPOINT=api.mailgun.net
```

**Option C: SendGrid**
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bamecrm.online
MAIL_FROM_NAME="BAME CRM"
```

**Option D: Generic SMTP**
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@bamecrm.online
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bamecrm.online
MAIL_FROM_NAME="BAME CRM"
```

#### Generate API Key Salt

Run this command locally to generate a secure API key salt:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

Copy the output and use it for `API_KEY_SALT`.

### 7. Configure Domain

In the **Domains** tab:

1. Click **"+ Add Domain"**
2. Enter your domain: `bamecrm.online` (or subdomain like `app.bamecrm.online`)
3. Coolify will automatically provision SSL certificate via Let's Encrypt
4. Ensure your domain's DNS A record points to your Hostinger VPS IP

### 8. Configure Health Check

In the **Health Check** tab:

- **Enabled:** ✅ Yes
- **Path:** `/up`
- **Port:** `$PORT` (default)
- **Interval:** 30 seconds

### 9. Set Post-Deployment Command

In the **Build** tab, add this post-deployment command:

```bash
php artisan migrate:fresh --force && php artisan setup:production
```

**Note:** This will drop all tables and re-run migrations, then setup default users and pipelines. For subsequent deployments after initial setup, change to:

```bash
php artisan migrate --force
```

### 10. Deploy!

1. Click **"Deploy"** button
2. Monitor the build logs in real-time
3. Wait for all three processes (web, worker, scheduler) to start
4. Check health status shows green

## Post-Deployment Verification

### 1. Test Application Access

Visit your domain: `https://bamecrm.online`

You should see the login page.

### 2. Test Default Login

Use the seeded credentials:

- **Admin:** admin@bamecrm.com / password
- **Manager:** manager@bamecrm.com / password
- **User:** user@bamecrm.com / password

### 3. Test Email Delivery

1. Go to **Forgot Password** page
2. Enter a test email
3. Check if email is received
4. Test the password reset flow

### 4. Test Queue Worker

1. Create a new lead or perform an action that triggers email
2. Check that emails are being sent (queue is processing)
3. Monitor logs in Coolify dashboard

### 5. Monitor Logs

In Coolify dashboard:
- Click on your application
- Go to **Logs** tab
- Monitor all three processes (web, worker, scheduler)

## Troubleshooting

### Build Fails

**Check:**
- Build logs in Coolify for specific errors
- Ensure `composer.json` and `package.json` are valid
- Verify PHP 8.2 compatibility

### Database Errors

**Check:**
- Persistent storage is mounted correctly
- Database file has write permissions
- Migration ran successfully in post-deployment command

### Queue Not Processing

**Check:**
- Worker process is running (green status)
- Check worker logs for errors
- Verify `QUEUE_CONNECTION=database` is set

### Email Not Sending

**Check:**
- Email service credentials are correct
- Check application logs for mail errors
- Test with `php artisan tinker` (via Coolify terminal)

### Scheduler Not Running

**Check:**
- Scheduler process is running
- Check scheduler logs
- Verify cron tasks are defined in `routes/console.php`

## Updating the Application

### Deploy New Changes

1. Push changes to GitHub
2. In Coolify, click **"Redeploy"**
3. Coolify will pull latest code and rebuild
4. Post-deployment commands run automatically

### Manual Commands

If you need to run artisan commands:

1. Go to **Terminal** tab in Coolify
2. Select the **web** process
3. Run commands:
   ```bash
   php artisan cache:clear
   php artisan config:cache
   php artisan queue:restart
   ```

## Backup Strategy

### Automated Backups

The application includes automated daily backups (configured in Laravel scheduler).

Backups are stored in: `/app/storage/app/backups/`

### Manual Backup

Via Coolify terminal:

```bash
php artisan backup:database
```

### Download Backups

1. Go to **Storage** tab
2. Browse to `/storage/app/backups/`
3. Download backup files

## Scaling Considerations

### Upgrade from SQLite to PostgreSQL

For better performance and scalability:

1. In Coolify, add a **PostgreSQL** database service
2. Update environment variables:
   ```bash
   DB_CONNECTION=pgsql
   DB_HOST=postgres
   DB_PORT=5432
   DB_DATABASE=bamecrm
   DB_USERNAME=bamecrm
   DB_PASSWORD=<generated>
   ```
3. Export SQLite data and import to PostgreSQL
4. Redeploy

### Add Redis for Caching

1. In Coolify, add a **Redis** service
2. Update environment variables:
   ```bash
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   REDIS_HOST=redis
   REDIS_PORT=6379
   ```
3. Redeploy

### Scale Queue Workers

In the **Processes** tab:
- Increase worker process replicas from 1 to 2 or more
- Adjust based on queue load

## Security Checklist

- ✅ `APP_DEBUG=false` in production
- ✅ Strong `APP_KEY` generated
- ✅ Secure `API_KEY_SALT` set
- ✅ HTTPS enabled (automatic with Coolify)
- ✅ Database credentials secured
- ✅ Email credentials secured
- ✅ File permissions handled by Coolify
- ✅ Rate limiting enabled (built into app)
- ✅ CSRF protection enabled (Laravel default)

## Monitoring

### Application Health

- Health check endpoint: `https://bamecrm.online/up`
- Coolify monitors this automatically

### Logs

Monitor in Coolify dashboard:
- **Web logs:** Application requests and errors
- **Worker logs:** Queue job processing
- **Scheduler logs:** Scheduled task execution

### Performance

- Monitor response times in Coolify metrics
- Check database query performance
- Monitor queue job processing times

## Support

For deployment issues:
- Check Coolify logs first
- Review this guide
- Check main `DEPLOYMENT.md` for application-specific details
- Contact Coolify support for platform issues

## Quick Reference

### Environment URLs

- **Application:** https://bamecrm.online
- **Coolify Dashboard:** https://apps.bamecrm.online
- **Health Check:** https://bamecrm.online/up

### Key Files

- `nixpacks.toml` - Build configuration
- `Procfile` - Process definitions
- `.env` - Environment variables (managed in Coolify)
- `DEPLOYMENT.md` - General deployment guide

### Common Commands (via Coolify Terminal)

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart queue
php artisan queue:restart

# Run migrations
php artisan migrate --force

# Create backup
php artisan backup:database

# Check application status
php artisan about
```

---

**Deployment Date:** _____________________

**Deployed By:** _____________________

**Notes:** _____________________
