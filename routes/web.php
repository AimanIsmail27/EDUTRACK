<?php

use Illuminate\Support\Facades\Route;

// This route handles the root URL (e.g., http://127.0.0.1:8000/)
Route::get('/', function () {
    // You can redirect to /login
    return redirect('/login'); 
});

// This route handles the /login URL (e.g., http://127.0.0.1:8000/login)
Route::get('/login', function () {
    return view('auth.login');
});