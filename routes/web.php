<?php

use App\Http\Controllers\BooksController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('books', [BooksController::class, 'index'])->name('books.index');

// Добавляем ? к параметрам и указываем значения по умолчанию в контроллере
Route::get('book/{allPath}', [BooksController::class, 'show'])
    ->where('allPath', '.*') // Разрешаем любые символы и слэши
    //->where('id', '[0-9]+') // Регулярное выражение: id должен быть только числом; встроенный фильтр роута (ограничение)
    ->name('books.show'); // Название пути. При смене маски, название не поменяется. Использется при редиректах.

Route::get('search', [SearchController::class, 'index'])
    ->name('search');
