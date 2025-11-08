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
      "email": "user@example.com"
    }
  }
}
```

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
Retrieve a paginated list of all departments.

**Endpoint:** `GET /api/departments`

**Authentication:** Required (Bearer token)

**Query Parameters:**
- None (pagination is handled automatically with 10 items per page)

**Success Response (200):**
```json
{
  "status": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "name": "Engineering",
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Marketing",
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
Create a new department.

**Endpoint:** `POST /api/departments`

**Authentication:** Required (Bearer token)

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

**Examples:**
- `GET /api/employees?department_id=1` - Get employees in department 1
- `GET /api/employees?name=John` - Get employees with "John" in their name
- `GET /api/employees?email=example.com` - Get employees with "example.com" in their email
- `GET /api/employees?salary_range=40000-60000` - Get employees with salary between 40000 and 60000
- `GET /api/employees?department_id=1&name=John` - Combine multiple filters

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
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Jane Smith",
      "email": "jane.smith@example.com",
      "department_id": 2,
      "salary": 60000.00,
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
Create a new employee. A welcome email job will be dispatched automatically upon creation.

**Endpoint:** `POST /api/employees`

**Authentication:** Required (Bearer token)

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

**Note:** Upon successful creation, a welcome email job is automatically dispatched to the queue.

---

#### Delete Employee
Soft delete an employee by ID.

**Endpoint:** `DELETE /api/employees/{id}`

**Authentication:** Required (Bearer token)

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

**Note:** This endpoint performs a soft delete, meaning the employee record is not permanently removed from the database.

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
curl -X GET http://your-domain.com/api/departments \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
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

#### Create Employee (Authenticated)
```bash
curl -X POST http://your-domain.com/api/employees \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "department_id": 1,
    "salary": 50000.00
  }'
```

#### Delete Employee (Authenticated)
```bash
curl -X DELETE http://your-domain.com/api/employees/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```
