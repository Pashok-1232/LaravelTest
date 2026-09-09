<?php
namespace App\Services;

use App\Models\Book;

class BookUrlGenerator
{
    /**
     * Генерирует текстовую часть пути: author/title/id или title/id
     */
    public function makePath(Book $book): string
    {
        // Загружаем авторов, если они вдруг еще не загружены (защита от N+1)
        if (!$book->relationLoaded('authors'))
        {
            $book->load('authors');
        }

        $firstAuthor = $book->authors->first();
        $segments = [];

        if ($firstAuthor && !empty($firstAuthor->translit))
        {
            $segments[] = $firstAuthor->translit;
        }

        $segments[] = $book->translit;
        $segments[] = $book->id;

        return implode('/', $segments);
    }
}
?>