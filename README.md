# Digital Product Store

A Laravel-based web application for selling and managing digital products with integrated payment systems, user roles, and stock management.

## Overview

This application serves as a comprehensive platform for selling digital products such as digital codes, software licenses, and online services. The system features multiple user roles (admin, customer, reseller), integrated QRIS payment, balance management, and detailed transaction tracking.

## Technology Stack

- **PHP**: ^8.1
- **Laravel**: ^10.10
- **Database**: MySQL
- **Frontend**: Bootstrap, Blade templates
- **Payment Integration**: QRIS

## Features

### User Management
- Multiple user roles (admin, customer, reseller)
- Authentication system (login, register, logout)
- User profile management

### Product Management
- Digital product catalog
- Product categorization
- Stock management for digital goods
- Stock serial number tracking

### Payment System
- QRIS payment integration
- Balance/deposit system for users
- Transaction history
- Multiple payment methods

### Admin Dashboard
- Comprehensive sales analytics
- User management interface
- Product and stock management
- Transaction monitoring and management

### User Dashboard
- Order history
- Balance management
- Digital product purchase flow
- Contact support system

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd Tugas-Akhir
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in .env file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

6. Compile assets:
```bash
npm run dev
```

7. Start the development server:
```bash
php artisan serve
```

## Usage

### Admin Access
- Navigate to `/login`
- Use admin credentials (check seeders for default admin)
- Access admin dashboard at `/admin/dashboard`

### Customer/Reseller Access
- Register at `/register` or login at `/login` with customer credentials
- Browse products at `/product`
- Purchase products and manage account

## Directory Structure

- `app/` - Application code
  - `Http/Controllers/` - Controllers for handling requests
  - `Models/` - Eloquent models
  - `Providers/` - Service providers
- `config/` - Configuration files
- `database/` - Migrations and seeders
- `public/` - Publicly accessible files
- `resources/` - Views, CSS, JavaScript, and language files
- `routes/` - Application routes
  - `web.php` - Web routes
  - `api.php` - API routes
- `storage/` - Application storage

## Admin Functionality

- Manage products and their stocks
- View and manage user accounts
- Monitor transactions and sales
- Generate and analyze sales reports

## User Functionality

- Browse available products
- Purchase products using balance or QRIS payment
- View transaction history
- Contact support
- Manage account balance through deposits

## Demo

A live demo of the application is available at: [https://motachi.xyz](https://motachi.xyz)

**Note:** The demo website will be available until August 17, 2025.

## License

This project is licensed under the MIT License - see the composer.json file for details.
