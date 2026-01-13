# PHP_Laravel12_Approve_Active_InActive_Users

A comprehensive user management system built with Laravel 12 that provides complete user lifecycle management, including approval, activation, deactivation, and deletion.

This project is suitable for learning purposes, interview preparation, and real-world admin panel use cases. It focuses on clean architecture, Laravel best practices, and secure user handling.

---

## Features

### User Management

* Full CRUD operations for users

### Approval System

* Approve or reject new user registrations

### Status Management

* Set users as active or inactive

### Pending Users

* Separate view for users awaiting approval

### Authentication

* Secure authentication using Laravel Breeze

### Responsive Design

* Works on all device sizes

### Pagination

* Efficient handling of large user lists

---

## Prerequisites

* PHP 8.2 or higher
* Composer
* MySQL 5.7 or higher
* Node.js and NPM

---

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/laravel-user-management.git
cd laravel-user-management
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Update database credentials in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=user_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Install Authentication (Optional)

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run dev
```

### 6. Seed Database (Optional)

```bash
php artisan db:seed
```

### 7. Start Development Server

```bash
php artisan serve
npm run dev
```

Visit: [http://localhost:8000](http://localhost:8000)
---
## Screenshot
<img width="1919" height="968" alt="image" src="https://github.com/user-attachments/assets/c88c0863-19b8-41bc-ac85-1564b327aebf" />
<img width="1877" height="968" alt="image" src="https://github.com/user-attachments/assets/c522d163-3dba-4c97-8abb-352680584095" />


---

## Default Login Credentials

Admin User

* Email: [admin@example.com](mailto:admin@example.com)
* Password: password

Pending User

* Email: [pending@example.com](mailto:pending@example.com)
* Password: password

Inactive User

* Email: [inactive@example.com](mailto:inactive@example.com)
* Password: password

---

## User Status Types

**Pending:**

* New users awaiting approval
* is_approved = false
* status = pending

**Active:**

* Approved and active users
* is_approved = true
* status = active

**Inactive:**

* Approved but deactivated users
* is_approved = true
* status = inactive

---

## Available Routes

| Method | URI                      | Description      |
| ------ | ------------------------ | ---------------- |
| GET    | /users                   | List all users   |
| GET    | /users/create            | Create user form |
| POST   | /users                   | Store user       |
| GET    | /users/{user}            | Show user        |
| GET    | /users/{user}/edit       | Edit user        |
| PUT    | /users/{user}            | Update user      |
| DELETE | /users/{user}            | Delete user      |
| POST   | /users/{user}/approve    | Approve user     |
| POST   | /users/{user}/activate   | Activate user    |
| POST   | /users/{user}/deactivate | Deactivate user  |
| GET    | /users/pending           | Pending users    |

---

## Security Features

* CSRF protection
* Password hashing
* Authentication middleware
* Role-based access control
* Eloquent ORM protection

---

## Testing

```bash
php artisan test
```

---

## License

MIT License

---

# Customer Request Management System

A Laravel-based customer request management system with a complete admin approval workflow. Customers can submit requests, and administrators can review, approve, or reject them.

This project is suitable for admin panels and workflow-driven applications.

---

## Features

### Admin Side

* View all customers with status
* Approve or reject customer registrations
* Activate or deactivate accounts
* Manage customer requests
* View request history

### Customer Side

* Register account (admin approval required)
* Login after approval
* Submit requests
* View request status
* Cancel pending requests
* View request history

---

## Installation

### Step 1: Clone and Setup

```bash
git clone <repository-url>
cd customer-request-system
composer install
cp .env.example .env
php artisan key:generate
```

### Step 2: Database Setup

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_system
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

### Step 3: Seed Database (Optional)

```bash
php artisan db:seed
```

Default Users

* Admin: [admin@example.com](mailto:admin@example.com) / password
* Customer (Pending): [pending1@example.com](mailto:pending1@example.com) / password
* Customer (Active): [active1@example.com](mailto:active1@example.com) / password

### Step 4: Install Frontend

```bash
npm install
npm run build
```

### Step 5: Start Server

```bash
php artisan serve
```

Visit: [http://localhost:8000](http://localhost:8000)

---

## Workflow

**Customer Registration:**

* Register at /register
* Account created with pending status
* Activation request sent to admin
* Login blocked until approval

**Admin Approval:**

* Admin logs in
* Approves customer
* Customer notified
* Customer can log in

**Request Management:**

* Customer submits request
* Admin reviews request
* Admin approves or rejects
* Customer notified

---

## Customization

**Add Request Types:**
Edit Request model:

```php
protected $fillable = ['type'];
```

**Status Options:**
Edit users migration:

```php
$table->enum('status', ['pending','active','inactive','suspended'])->default('pending');
```

---

## Common Issues

**Route errors:**

```bash
php artisan route:clear
```

**View errors:**

```bash
php artisan view:clear
```

**Database issues:**

```bash
php artisan migrate
```

---

## Testing

```bash
php artisan test
php artisan optimize:clear
```

---

## License

MIT License

