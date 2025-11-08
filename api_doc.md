# API Documentation

## Mini Employee Management System API

This document describes the REST API endpoints for the Mini Employee Management System.

---

## Base URL

All API endpoints are prefixed with `/api`.

**Example:** `http://your-domain.com/api`

---

## Authentication

Most endpoints require authentication using Laravel Sanctum. After successful login, include the token in the `Authorization` header as a Bearer token.

**Header Format:**
```
Authorization: Bearer {token}
```

### Admin-Only Endpoints

Some endpoints require admin privileges. For these endpoints, you must include an additional header:

**Header Format:**
```
X-ROLE: admin
```

**Admin-Protected Endpoints:**
- `POST /api/departments` - Create department
- `POST /api/employees` - Create employee
- `DELETE /api/employees/{id}` - Delete employee

**Error Response (403) for Admin-Only Endpoints:**
```json
{
  "status": false,
  "message": "Unauthorized. Admin access required."
}
```

---

## Response Format

### Success Response
```json
{
  "status": true,
  "message": "Success message",
  "data": { ... }
}
```

### Error Response
```json
{
  "status": false,
  "message": "Error message",
  "errors": { ... }
}
```

### Paginated Response
```json
{
  "status": true,
  "message": "Success",
  "data": [ ... ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 50
  }
}
```

---

## Endpoints

### 1. Authentication

#### Login
Authenticate a user and receive an access token.

**Endpoint:** `POST /api/login`

**Authentication:** Not required

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Validation Rules:**
- `email`: required, must be a valid email
- `password`: required

**Success Response (200):**
```json
{
  "status": true,
  "message": "Logged in successfully",
  "data": {
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "user": {
      "name": "John Doe",
      "email": "user@example.com",
      "role": "admin"
    }
  }
}
```

**Note:** The `role` field indicates the user's role (`admin` or `user`). Store this value along with the token for role-based access control on the frontend.

**Error Response (401):**
```json
{
  "status": false,
  "message": "Invalid credentials",
  "errors": null
}
```

**Validation Error Response (422):**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

---

### 2. Departments

#### List All Departments
Retrieve a paginated list of all departments with employee counts. Supports searching by department name.

**Endpoint:** `GET /api/departments`

**Authentication:** Required (Bearer token)

**Query Parameters:**
- `name` (optional): Filter departments by name (partial match, case-insensitive)
- `page` (optional): Page number for pagination (default: 1)

**Examples:**
- `GET /api/departments` - Get all departments
- `GET /api/departments?name=Engineering` - Search departments by name
- `GET /api/departments?page=2` - Get second page of results

**Note:** Pagination is handled automatically with 10 items per page.

**Success Response (200):**
```json
{
  "status": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "name": "Engineering",
      "employees_count": 5,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Marketing",
      "employees_count": 3,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 2
  }
}
```

**Note:** The `employees_count` field indicates the number of employees in each department.

**Error Response (401):**
```json
{
  "status": false,
  "message": "Unauthenticated",
  "errors": null
}
```

---

#### Create Department
Create a new department. **Admin access required.**

**Endpoint:** `POST /api/departments`

**Authentication:** Required (Bearer token)

**Required Headers:**
- `Authorization: Bearer {token}`
- `X-ROLE: admin`

**Request Body:**
```json
{
  "name": "Engineering"
}
```

**Validation Rules:**
- `name`: required, string, max 255 characters, must be unique

**Success Response (200):**
```json
{
  "status": true,
  "message": "Department created",
  "data": {
    "id": 1,
    "name": "Engineering",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

**Validation Error Response (422):**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "name": [
      "The name field is required.",
      "The name has already been taken."
    ]
  }
}
```

**Error Response (401):**
```json
{
  "status": false,
  "message": "Unauthenticated",
  "errors": null
}
```

**Error Response (403):**
```json
{
  "status": false,
  "message": "Unauthorized. Admin access required."
}
```

---

### 3. Employees

#### List All Employees
Retrieve a paginated list of all employees with their department information. Supports filtering by department, name, email, and salary range.

**Endpoint:** `GET /api/employees`

**Authentication:** Required (Bearer token)

**Query Parameters:**
- `department_id` (optional): Filter employees by department ID
- `name` (optional): Filter employees by name (partial match, case-insensitive)
- `email` (optional): Filter employees by email (partial match, case-insensitive)
- `salary_range` (optional): Filter employees by salary range (format: `min-max`, e.g., `30000-70000`)
- `page` (optional): Page number for pagination (default: 1)

**Examples:**
- `GET /api/employees?department_id=1` - Get employees in department 1
- `GET /api/employees?name=John` - Get employees with "John" in their name
- `GET /api/employees?email=example.com` - Get employees with "example.com" in their email
- `GET /api/employees?salary_range=40000-60000` - Get employees with salary between 40000 and 60000
- `GET /api/employees?department_id=1&name=John&salary_range=30000-70000` - Combine multiple filters
- `GET /api/employees?page=2` - Get second page of results

**Note:** Pagination is handled automatically with 10 items per page. Filters can be combined.

**Success Response (200):**
```json
{
  "status": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "department_id": 1,
      "salary": 50000.00,
      "department": {
        "id": 1,
        "name": "Engineering"
      },
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Jane Smith",
      "email": "jane.smith@example.com",
      "department_id": 2,
      "salary": 60000.00,
      "department": {
        "id": 2,
        "name": "Marketing"
      },
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 2
  }
}
```

**Note:** The `department` object contains the department's `id` and `name` for each employee.

**Error Response (401):**
```json
{
  "status": false,
  "message": "Unauthenticated",
  "errors": null
}
```

---

#### Create Employee
Create a new employee. A welcome email job will be dispatched automatically upon creation. **Admin access required.**

**Endpoint:** `POST /api/employees`

**Authentication:** Required (Bearer token)

**Required Headers:**
- `Authorization: Bearer {token}`
- `X-ROLE: admin`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "department_id": 1,
  "salary": 50000.00
}
```

**Validation Rules:**
- `name`: required, string, max 255 characters
- `email`: required, must be a valid email, must be unique in employees table, max 255 characters
- `department_id`: required, must exist in departments table
- `salary`: required, must be numeric, minimum value 0

**Success Response (200):**
```json
{
  "status": true,
  "message": "Employee created",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "department_id": 1,
    "salary": 50000.00,
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

**Validation Error Response (422):**
```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."],
    "email": [
      "The email field is required.",
      "The email has already been taken."
    ],
    "department_id": [
      "The department id field is required.",
      "The selected department id is invalid."
    ],
    "salary": [
      "The salary field is required.",
      "The salary must be a number.",
      "The salary must be at least 0."
    ]
  }
}
```

**Error Response (401):**
```json
{
  "status": false,
  "message": "Unauthenticated",
  "errors": null
}
```

**Error Response (403):**
```json
{
  "status": false,
  "message": "Unauthorized. Admin access required."
}
```

**Note:** Upon successful creation, a welcome email job is automatically dispatched to the queue.

---

#### Delete Employee
Soft delete an employee by ID. **Admin access required.**

**Endpoint:** `DELETE /api/employees/{id}`

**Authentication:** Required (Bearer token)

**Required Headers:**
- `Authorization: Bearer {token}`
- `X-ROLE: admin`

**URL Parameters:**
- `id`: The ID of the employee to delete (route model binding)

**Success Response (200):**
```json
{
  "status": true,
  "message": "Employee deleted",
  "data": null
}
```

**Error Response (404):**
```json
{
  "status": false,
  "message": "Employee not found",
  "errors": null
}
```

**Error Response (401):**
```json
{
  "status": false,
  "message": "Unauthenticated",
  "errors": null
}
```

**Error Response (403):**
```json
{
  "status": false,
  "message": "Unauthorized. Admin access required."
}
```

**Note:** This endpoint performs a soft delete, meaning the employee record is not permanently removed from the database.

---

#### Get Maximum Salary
Retrieve the maximum salary from all employees. Useful for setting up salary range filters.

**Endpoint:** `GET /api/employees/max-salary`

**Authentication:** Required (Bearer token)

**Success Response (200):**
```json
{
  "status": true,
  "message": "Max salary retrieved",
  "data": {
    "max_salary": 100000.00
  }
}
```

**Error Response (401):**
```json
{
  "status": false,
  "message": "Unauthenticated",
  "errors": null
}
```

**Note:** Returns `0` if no employees exist in the database.

---

## HTTP Status Codes

| Code | Description |
|------|-------------|
| 200  | Success |
| 401  | Unauthenticated - Missing or invalid token |
| 403  | Forbidden - Insufficient permissions |
| 404  | Not Found - Resource not found |
| 422  | Validation Error - Request validation failed |
| 500  | Server Error - Internal server error |

---

## Error Handling

All errors follow a consistent format:

```json
{
  "status": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

For validation errors, the `errors` object contains field-specific error messages. For other errors, `errors` may be `null`.

---

## Example Usage

### cURL Examples

#### Login
```bash
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

#### Get Departments (Authenticated)
```bash
# Get all departments
curl -X GET http://your-domain.com/api/departments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"

# Search departments by name
curl -X GET "http://your-domain.com/api/departments?name=Engineering" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

#### Create Department (Admin Only)
```bash
curl -X POST http://your-domain.com/api/departments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-ROLE: admin" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Engineering"
  }'
```

#### Get Employees with Filters (Authenticated)
```bash
# Get employees by department
curl -X GET "http://your-domain.com/api/employees?department_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"

# Get employees by name
curl -X GET "http://your-domain.com/api/employees?name=John" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"

# Get employees by salary range
curl -X GET "http://your-domain.com/api/employees?salary_range=40000-60000" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"

# Combine multiple filters
curl -X GET "http://your-domain.com/api/employees?department_id=1&name=John&salary_range=30000-70000" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

#### Create Employee (Admin Only)
```bash
curl -X POST http://your-domain.com/api/employees \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-ROLE: admin" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "department_id": 1,
    "salary": 50000.00
  }'
```

#### Delete Employee (Admin Only)
```bash
curl -X DELETE http://your-domain.com/api/employees/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "X-ROLE: admin" \
  -H "Content-Type: application/json"
```

#### Get Maximum Salary (Authenticated)
```bash
curl -X GET http://your-domain.com/api/employees/max-salary \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```
