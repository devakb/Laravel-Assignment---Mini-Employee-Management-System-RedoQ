<?php

namespace App\Http\Controllers\Api;

use App\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequests\StoreDepartmentRequest;
use App\Models\Department;

class DepartmentController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $departments = Department::paginate(10);

        return $this->paginatedResponse($departments);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create($request->all());

        return $this->successResponse($department, 'Department created');
    }

}
