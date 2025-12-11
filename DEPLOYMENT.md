# Production Deployment Guide

## Pre-Deployment Checklist

### 1. Environment Configuration

Copy `.env.example` to `.env` and configure the following:

```bash
# Application
APP_NAME="BAME CRM"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=base64:... # Generate with: php artisan key:generate

# Database
DB_CONNECTION=sqlite
# For production, consider PostgreSQL or MySQL for better scalability

# Email Service (Choose one option)
```

### 2. Email Service Setup

Choose one of the following email providers:

#### Option A: ZeptoMail (Recommended - 10,000 emails/month free)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.zeptomail.com
MAIL_PORT=587
MAIL_USERNAME=emailapikey
MAIL_PASSWORD=your-zeptomail-send-token
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="BAME CRM"
```

**Setup Steps:**
1. Sign up at https://www.zoho.com/zeptomail/
2. Verify your domain in ZeptoMail dashboard
3. Add DNS records (SPF, DKIM, CNAME) provided by ZeptoMail
4. Go to **Mail Agents** → **SMTP** → Create new SMTP user
5. Copy the **Send Token** (use as MAIL_PASSWORD)
6. Username is always `emailapikey`
7. Verify sender address in ZeptoMail dashboard

#### Option B: Mailgun (Free tier: 5,000 emails/month)
```env
MAIL_MAILER=mailgun
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="BAME CRM"
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=your-mailgun-api-key
MAILGUN_ENDPOINT=api.mailgun.net
```

**Setup Steps:**
1. Sign up at https://www.mailgun.com/
2. Verify your domain
3. Get your API key from dashboard
4. Add DNS records (SPF, DKIM, CNAME)

#### Option C: SendGrid (Reliable, 100 emails/day free)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="BAME CRM"
```

**Setup Steps:**
1. Sign up at https://sendgrid.com/
2. Create an API key
3. Verify sender identity
4. Add API key to `.env`

#### Option D: AWS SES (Cost-effective for high volume)
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-aws-key
AWS_SECRET_ACCESS_KEY=your-aws-secret
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="BAME CRM"
```

**Setup Steps:**
1. Create AWS account
2. Verify email/domain in SES
3. Request production access (starts in sandbox)
4. Create IAM user with SES permissions

#### Option E: Generic SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="BAME CRM"
```

### 3. API Security

Generate a secure API key for external integrations:

```bash
# Generate random string (32+ characters)
php -r "echo bin2hex(random_bytes(32));"
```

Add to `.env`:
```env
API_KEY_SALT=your-generated-secure-key
```

### 4. Logging Configuration

```env
LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DAILY_DAYS=14
```

### 5. Cache & Session

```env
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

## Deployment Steps

### 1. Install Dependencies

```bash
# Install PHP dependencies (production only)
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install
npm run build
```

### 2. Database Setup

```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate --force

# Seed default users
php artisan db:seed --class=DefaultUserSeeder
```

### 3. Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 4. Set Permissions

```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (run as administrator)
icacls storage /grant Users:(OI)(CI)F /T
icacls bootstrap/cache /grant Users:(OI)(CI)F /T
```

### 5. Setup Scheduled Tasks

Add to crontab (Linux/Mac):
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Windows Task Scheduler:
```bash
Program: C:\path\to\php.exe
Arguments: C:\path\to\project\artisan schedule:run
Trigger: Every 1 minute
```

### 6. Setup Queue Worker

#### Using Supervisor (Linux - Recommended)

Create `/etc/supervisor/conf.d/bamecrm-worker.conf`:
```ini
[program:bamecrm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bamecrm-worker:*
```

#### Using Windows Service

Install NSSM (Non-Sucking Service Manager):
```bash
nssm install BameCRMWorker "C:\path\to\php.exe" "C:\path\to\project\artisan queue:work database --sleep=3 --tries=3"
nssm start BameCRMWorker
```

### 7. Web Server Configuration

#### Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /path-to-your-project/public;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache (.htaccess already included)
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /path-to-your-project/public

    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem

    <Directory /path-to-your-project/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Post-Deployment

### 1. Test Email Delivery

```bash
php artisan tinker
>>> \Illuminate\Support\Facades\Mail::raw('Test email', function($msg) { $msg->to('your-email@example.com')->subject('Test'); });
```

### 2. Test Password Reset

1. Visit `/forgot-password`
2. Enter a user email
3. Check email delivery
4. Test reset link

### 3. Test API Endpoint

```bash
curl -X POST https://yourdomain.com/api/leads/ingest \
  -H "X-API-KEY: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "company_name": "Test Company",
    "email": "test@example.com",
    "source": "API Test"
  }'
```

### 4. Monitor Logs

```bash
# Watch application logs
tail -f storage/logs/laravel.log

# Watch queue worker logs
tail -f storage/logs/worker.log
```

## Backup Strategy

### Automated Daily Backups

The system includes an automated backup command. Schedule it:

```bash
# In routes/console.php (already configured)
Schedule::command('backup:database')->daily()->at('02:00');
```

Backups are stored in `storage/app/backups/` with 7-day retention.

### Manual Backup

```bash
php artisan backup:database
```

## Monitoring & Maintenance

### Health Check

Visit: `https://yourdomain.com/up`

### Clear Caches (if needed)

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Update Application

```bash
git pull origin main
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

## Troubleshooting

### Email Not Sending

1. Check `.env` email configuration
2. Test with `php artisan tinker`
3. Check `storage/logs/laravel.log`
4. Verify DNS records (SPF, DKIM)
5. Check spam folder

### Queue Not Processing

1. Check queue worker is running: `supervisorctl status`
2. Restart worker: `php artisan queue:restart`
3. Check worker logs: `storage/logs/worker.log`

### Performance Issues

1. Enable OPcache in `php.ini`
2. Use Redis for cache/sessions (optional)
3. Add database indexes (already included)
4. Consider upgrading from SQLite to PostgreSQL/MySQL

## Security Checklist

- ✅ HTTPS enforced
- ✅ APP_DEBUG=false
- ✅ Strong APP_KEY generated
- ✅ Secure API_KEY_SALT
- ✅ Rate limiting enabled
- ✅ CSRF protection enabled
- ✅ Content Security Policy active
- ✅ File permissions set correctly
- ✅ Database credentials secured
- ✅ Email credentials secured

## Support

For issues or questions:
- Check logs: `storage/logs/laravel.log`
- Review this deployment guide
- Contact system administrator
