<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DBSearchRepository
{
    /**
     * Ищет ID книг по полнотекстовому индексу в сторонней таблице.
     */
    public function findBooksIdsByQuery(string $searchQuery): Collection
    {
        $booleanQuery = $this->makeStringForBooleanQuery($searchQuery);

        // Вот он — чистый Query Builder через фасад DB без использования моделей
        return DB::table('books_searches')
            ->whereFullText('search', $booleanQuery, ['mode' => 'boolean'])
            ->pluck('bookId'); // Возвращает коллекцию ID
    }

    public function findAuthorsIdsByQuery(string $searchQuery): Collection
    {
        $booleanQuery = $this->makeStringForBooleanQuery($searchQuery);

        // Выполняем сырой MATCH AGAINST запрос к таблице авторов без моделей
        $result = DB::table('authors')
            ->select('id')
            ->whereRaw(
                "MATCH(first, second, third) AGAINST(? IN BOOLEAN MODE)",
                [$booleanQuery]
            )
            ->pluck('id'); // Возвращаем только ID найденных авторов

        return $result;
    }

    private function makeStringForBooleanQuery(string $query): string
    {
        // Из "Роберт Мартин" сделает "+Роберт +Мартин"
        $words = explode(' ', $query);
        $booleanQuery = '';
        foreach ($words as $word)
        {
            $word = trim($word);
            if (!empty($word))
            {
                $booleanQuery .= (mb_strlen($word) > 2 ? '+' . $word.' ' : $word.' ');
            }
        }

        return trim($booleanQuery);
    }
}
?>