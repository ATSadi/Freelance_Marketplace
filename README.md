# WorkVault

A Laravel-based escrow and milestone payment tracking platform connecting clients and freelancers.

## Tech Stack

- **Laravel** (latest stable)
- **PostgreSQL**
- **Blade** templates

## Setup Instructions

### Prerequisites

- PHP 8.2+ with extensions: `openssl`, `pdo_pgsql`, `mbstring`, `fileinfo`, `curl`, `zip`
- [Composer](https://getcomposer.org/)
- PostgreSQL 14+

### Installation

1. Clone the repository and install dependencies:

```bash
composer install
```

2. Copy the environment file and configure your database:

```bash
cp .env.example .env
php artisan key:generate
```

3. Set PostgreSQL credentials in `.env`:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=workvault
DB_USERNAME=postgres
DB_PASSWORD=your_password_here
```

4. Create the PostgreSQL database:

```sql
CREATE DATABASE workvault;
```

5. Run migrations:

```bash
php artisan migrate
```

6. Start the development server:

```bash
php artisan serve
```

Visit `http://127.0.0.1:8000` to see the application.

## Project Roadmap

1. Project Setup & Database Configuration
2. Authentication & Roles
3. User Profiles
4. Project Posting
5. Proposals / Bidding
6. Proposal Management
7. Milestone System
8. Milestone Submission & Approval
9. Mock Escrow & Payments
10. Transaction History
11. Dispute System
12. Admin Panel
13. PDF Invoice Generation
14. Notifications
15. Search, Filters & UI Polish

## License

University lab project.
