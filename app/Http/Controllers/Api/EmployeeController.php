<?php

namespace App\Http\Controllers\Api;

use App\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequests\StoreEmployeeRequest;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $employees = Employee::query()
        ->when($request->has('department_id'), function ($query) use ($request) {
            $query->where('department_id', $request->department_id);
        })
        ->when($request->has('name'), function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->name . '%');
        })
        ->when($request->has('email'), function ($query) use ($request) {
            $query->where('email', 'like', '%' . $request->email . '%');
        })
        ->when($request->has('salary_range'), function ($query) use ($request) {
            $salaryRange = explode('-', $request->salary_range);
            if(is_numeric($salaryRange[0]) && is_numeric($salaryRange[1])) {
                $query->whereBetween('salary', [$salaryRange[0], $salaryRange[1]]);
            }
        })
        ->paginate(10);
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
