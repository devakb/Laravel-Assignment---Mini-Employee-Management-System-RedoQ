# RedoQ Software Services Private Limited

## Laravel Assignment – Mini Employee Management System

## Objective

Build a small Employee Management System using Laravel 11 to demonstrate your understanding
of database design, Eloquent ORM, middleware, validation, RESTful APIs, and queued jobs.

## 📚 Documentation

- **[API Documentation](api_doc.md)** - Complete API endpoint documentation with request/response examples

## 🚀 Installation Guide

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js and NPM (for frontend assets)
- Database (MySQL, PostgreSQL, SQLite, or SQL Server)

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd MiniEmpMgtSys
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

Edit the `.env` file and configure your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**For SQLite (Quick Start):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

Or create the SQLite database file:
```bash
touch database/database.sqlite
```

### Step 5: Run Migrations

```bash
# Run database migrations
php artisan migrate

# Seed the database with sample users
php artisan db:seed
```

**Default Users Created:**
- **Admin User:**
  - Email: `admin@admin.com`
  - Password: `password` (default from UserFactory)
  - Role: `admin`

- **General User:**
  - Email: `user@user.com`
  - Password: `password` (default from UserFactory)
  - Role: `user`

> **Note:** The default password is typically `password`. If you need to change it, you can update the UserFactory or modify the seeder directly.

### Step 6: Start the Development Server

```bash
# Start Laravel development server
php artisan serve

# In a separate terminal, start the queue worker (for email jobs)
php artisan queue:work
```

The application will be available at `http://localhost:8000`

### Step 7: Access the Application

1. **Web Interface:**
   - Login: `http://localhost:8000/login`
   - Departments: `http://localhost:8000/departments`
   - Employees: `http://localhost:8000/employees`

2. **API Endpoints:**
   - Base URL: `http://localhost:8000/api`
   - See [API Documentation](api_doc.md) for complete endpoint details


## 📖 API Documentation

For detailed API documentation including authentication, endpoints, request/response formats, and examples, please refer to [api_doc.md](api_doc.md).

---

## Project Requirements

### 1. Core Entities
Create two tables: departments and employees, with proper relationships. Each employee belongs
to one department, and each department can have multiple employees.

### 2. API Endpoints

Action | Method | Endpoint | Notes
------ | ------ | -------- | -----
List all departments | GET | /departments | Return all departments
Create department | POST | /departments | Add a new department
List all employees | GET | /employees | Include department names
Create employee | POST | /employees | Restricted to admin only
Delete employee | DELETE | /employees/{id} | Soft delete employee

### 3. Middleware
Create a middleware named EnsureAdmin that checks if the request header 'X-ROLE' equals
'admin'. If not, it should return a 403 Unauthorized response. Apply it only to routes that create or
delete records.

### 4. Validation
Use Laravel Form Request validation for the employee creation endpoint. Ensure: Name and Email
are required, Email is unique, Department exists, and Salary is numeric.

### 5. Queued Job
When a new employee is created, dispatch a queued job (e.g., SendWelcomeEmailJob) that logs a
welcome message to storage/logs/laravel.log.

Bonus (Optional)

- Add authentication using Laravel Sanctum.
- Add pagination and filtering for the employee list.
- Add a Blade UI page showing all employees with their department names.
- Add unit or feature tests.
