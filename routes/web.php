<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return 'Hello from Laravel!';
});

Route::get('/hello/{name}', function (string $name) {
    return view('hello', ['name' => $name]);
});

Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
