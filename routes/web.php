<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/departments', function () {
    return view('departments');
})->name('departments');

Route::get('/employees', function () {
    return view('employees');
})->name('employees');
