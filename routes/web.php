<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/classes', function () {
    return view('classes');
})->name('classes');

Route::get('/login', function () {
    return view('login');
})->name('login');
