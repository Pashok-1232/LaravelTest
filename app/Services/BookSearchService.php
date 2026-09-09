<?php
namespace App\Services;

use App\Repositories\DBSearchRepository;
use App\Models\Book;
use App\Models\Author;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookSearchService
{
    // Внедряем репозиторий через Type Hinting в конструкторе
    public function __construct(
        protected DBSearchRepository $searchRepository
    ) {}

    /**
     * Бизнес-логика: связываем поиск по индексу и выгрузку моделей Book
     */
    public function searchAll(array $filters): LengthAwarePaginator
    {
        if(isset($filters['type']))
        {
            if($filters['type'] == 'authors')
            {
                $authorsIds = $this->searchRepository->findAuthorsIdsByQuery($filters['search']);
                return Author::whereIn('id', $authorsIds)
                    ->paginate(15);

                // Сценарий А: Нужны авторы найденных книг! Нет! Только авторы.
                // Используем мощь связей из 3 дня: выбираем авторов, у которых ЕСТЬ книги из списка $bookIds
                /*$result = Author::whereHas('books', function ($query) use ($bookIds) {
                    $query->whereIn('books.id', $bookIds); // Указываем таблицу явно во избежание неоднозначности
                })
                ->paginate(15);*/
            }
            
        }
        //else
        //{
            // Шаг 1: Ищем ID через репозиторий (низкоуровневый DB запрос)
            $bookIds = $this->searchRepository->findBooksIdsByQuery($filters['search']);
            // Шаг 2: Достаем полноценные модели со связями
            return Book::with('authors')
                ->whereIn('id', $bookIds)
                ->paginate(15);
        //}
        
        //return $result;
    }
}
?>