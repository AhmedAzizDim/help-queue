<?php
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return 'Hello from Laravel!';
});

Route::get('/hello/aziz', function () {
    return view('hello', ['name' => 'aziz']);
});
Route::get('/tickets', [TicketController::class, 'index']);