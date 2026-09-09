<?php

use App\Http\Controllers\BooksController;
use App\Repositories\DBSearchTableUpdateRepository;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:create-book', function () {
    // Вызываем метод через контейнер Laravel
    $result = app(BooksController::class)->createBook();
    
    // Если контроллер вернул массив или объект, выведем его красиво в консоль
    if (is_array($result) || is_object($result)) {
        print_r($result);
    } else {
        $this->info($result);
    }
})->purpose('Создать книгу через контроллер');

Artisan::command('app:insertFullText', function(){
    $rep = new DBSearchTableUpdateRepository();
    $data = [
        ['bookId' => 4, 'search' => 'Мартин Роберт Сесил. Чистый код'],
        ['bookId' => 5, 'search' => 'Доу Джон. Чистый код'],
    ];
    $rep->insertQuery($data);
})->purpose('Вставка значения в таблицу books_searches');
