# RedoQ Software Services Private Limited

## Laravel Assignment – Mini Employee Management System

## Objective

Build a small Employee Management System using Laravel 11 to demonstrate your understanding
of database design, Eloquent ORM, middleware, validation, RESTful APIs, and queued jobs.

## Requirements

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
