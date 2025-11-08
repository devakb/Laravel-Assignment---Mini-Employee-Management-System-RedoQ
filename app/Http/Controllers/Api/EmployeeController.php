<?php

namespace App\Http\Controllers\Api;

use App\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequests\StoreEmployeeRequest;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\Employee;

class EmployeeController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $employees = Employee::paginate(10);
        return $this->paginatedResponse($employees);
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employee = Employee::create($request->all());

        SendWelcomeEmailJob::dispatch($employee);

        return $this->successResponse($employee, 'Employee created');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return $this->successResponse(null, 'Employee deleted');
    }
}
