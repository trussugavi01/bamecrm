# B.A.M.E Sponsorship Engagement CRM

A lightweight, high-performance CRM built with Laravel 11 and Livewire 3 to manage the sponsorship lifecycle for the B.A.M.E H&C Awards 2026.

## Features

- **Kanban Pipeline Management** - Drag-and-drop interface with 6 active stages
- **Auto-Probability Calculation** - Automatically updates based on stage
- **Stagnation Detection** - Visual indicators for deals inactive >14 days
- **Validation Gates** - Prevents invalid stage transitions
- **Form Builder** - Create customizable public lead capture forms
- **Executive Dashboard** - Pipeline health, funnel view, and win/loss metrics
- **User Management** - Role-based access control (Admin, Consultant, Executive, Approver)
- **Activity Logging** - Complete audit trail of all deal changes
- **API Integration** - External lead ingestion endpoint

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Database**: SQLite (portable)
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Charts**: Chart.js
- **Drag & Drop**: SortableJS

## Installation

### Requirements
- PHP 8.2 or higher
- Composer
- Node.js & NPM

### Setup

1. **Clone the repository**
```bash
git clone <repository-url>
cd bamecrm
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=DefaultUserSeeder
php artisan db:seed --class=SampleSponsorshipsSeeder
```

5. **Build assets**
```bash
npm run build
```

6. **Start development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` and login with:
- **Admin**: admin@bamecrm.com / password
- **Consultant**: consultant@bamecrm.com / password
- **Executive**: executive@bamecrm.com / password

## Deployment to Sevalla

### Pre-Deployment Checklist

1. **Update .env for production**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

2. **Optimize for production**
```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Set permissions**
```bash
chmod -R 775 storage bootstrap/cache
```

### Sevalla Deployment Steps

1. Push your code to a Git repository
2. Connect repository to Sevalla
3. Set environment variables in Sevalla dashboard
4. Configure build command: `composer install && npm run build`
5. Set web directory to `/public`
6. Deploy!

## Default User Roles

- **Admin (Ruth)** - Full CRUD access, can manage users and forms
- **Consultant (Hap Team)** - Create, Read, Update (no delete)
- **Executive (Leadership)** - Read-only, access to dashboards and reports
- **Approver** - Read-only, limited visibility (Proposal & Negotiation stages only)

## API Usage

### Lead Ingestion Endpoint

**Endpoint**: `POST /api/leads/ingest`

**Headers**:
```
X-API-KEY: your-api-key-here
Content-Type: application/json
```

**Payload**:
```json
{
  "company_name": "Acme Corporation",
  "contact_name": "John Doe",
  "email": "john@acme.com",
  "source": "Partner API",
  "message": "Interested in Gold tier sponsorship"
}
```

**Response**: 201 Created

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
