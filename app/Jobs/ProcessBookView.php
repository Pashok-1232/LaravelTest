<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

// Интерфейс ShouldQueue указывает Laravel, что задачу нужно положить в очередь, а не выполнять сразу
class ProcessBookView implements ShouldQueue
{
    use Queueable;

    protected $book;

    /**
     * Create a new job instance.
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Execute the job. выполнится воркером в ФОНЕ
     */
    public function handle(): void
    {
        // Эмулируем тяжелую работу или пишем логику
        // Например, увеличиваем счетчик просмотров книги в БД
        $this->book->increment('views_count'); 
        
        // (Обязательно добавь поле views_count в миграцию книг, если захочешь протестировать этот код, 
        // либо замени строку на любой другой тестовый код, например, \Log::info("Книгу посмотрели");)
    }
}
