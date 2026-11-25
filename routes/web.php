<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\TopicController;
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

Route::resource('tickets', TicketController::class)
    ->only(['index', 'create', 'store', 'show', 'update', 'destroy']);

Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
