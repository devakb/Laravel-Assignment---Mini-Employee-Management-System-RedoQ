<?php

use App\Http\Controllers\Api\{AuthController, DepartmentController, EmployeeController};
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store'])->middleware('admin');

    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store'])->middleware('admin');
    Route::delete('/employees/{employee:id}', [EmployeeController::class, 'destroy'])->middleware('admin');
    Route::get('/employees/max-salary', [EmployeeController::class, 'getMaxSalary']);

});
