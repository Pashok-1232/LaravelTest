<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\BookUrlGenerator;

class BooksController extends Controller
{
    // Laravel автоматически передаст сюда экземпляр BookUrlGenerator через DI
    public function index(BookUrlGenerator $urlGenerator)
    {
        // Получаем книги вместе с их авторами, упорядочивая по ID
        // paginate() автоматически подхватит GET-параметр ?page из URL
        $books = Book::with('authors')->orderBy('id', 'desc')->paginate(1);

        // Передаем коллекцию книг в шаблон списка
        return view('books.list', compact('books', 'urlGenerator'));
    }

    public function show($allPath, BookUrlGenerator $urlGenerator)
    {
        $params = explode('/', $allPath);

        $id = array_pop($params);
        if (!is_numeric($id))
            abort(404);

        $author = null;
        $title = null;

        if (count($params) === 2)
        {
            [$author, $title] = $params; // Если осталось 2 элемента, значит передан и автор, и название
        }
        elseif (count($params) === 1)
        {
            $title = $params[0]; // Если остался 1 элемент, значит автора нет, передано только название
        }
        else
            abort(404);

        // 1. Ищем книгу по ID вместе со связанными авторами (Eager Loading)
        // Если не нашли — Laravel автоматически выбросит 404 ошибку
        $book = Book::with('authors')->findOrFail($id);

        $correctPath = $urlGenerator->makePath($book);

        // Код ниже, но только через DI
        if ($allPath !== $correctPath) 
        {
            return redirect()->route('books.show', ['allPath' => $correctPath], 301);
        }

        // [НОВОЕ] Отправляем задачу в очередь. 
        // Контроллер не будет выполнять метод handle(), он просто бросит запись в таблицу jobs и пойдет дальше
        \App\Jobs\ProcessBookView::dispatch($book);

        // 2. Определяем правильный транслит автора для URL
        /*$correctAuthorTranslit = null;
        if($book->authors->first())
            $correctAuthorTranslit = $book->authors->first()->translit;

        // 3. Определяем правильный транслит названия книги
        $correctTitleTranslit = $book->translit;

        // 4. Проверяем: совпадает ли текущий URL с эталонным из БД?
        if ($author !== $correctAuthorTranslit || $title !== $correctTitleTranslit)
        {
            // Если не совпадает (или параметры не были переданы),
            // делаем постоянный 301-редирект на каноничный (правильный) адрес
            $routeParams = [
                'title'  => $correctTitleTranslit,
                'id'     => $book->id
            ];

            // Добавляем автора в массив параметров, ТОЛЬКО если он реально есть у книги
            if ($correctAuthorTranslit !== null)
            {
                // Вставляем на первое место для правильного порядка в URL
                $routeParams = ['author' => $correctAuthorTranslit] + $routeParams;
            }

            return redirect()->route('books.show', $routeParams, 301);
        }
        */
        // 5. Если URL идеальный — передаем модель в созданный нами Blade-шаблон
        return view('books.book', compact('book'));
    }

    public function createBook()
    {
        try
        {
            // Оборачиваем всё в транзакцию, так как у нас вставка в три таблицы
            $result = DB::transaction(function () {
                
                // 1. Создаем книгу
                // Поле flags не указываем, так как в миграции мы задали default(0)
                $book = Book::create([
                    'name'      => 'Чистый код',
                ]);

                // Данные для двух авторов
                $authorsData = [
                    [
                        'first'  => 'Роберт',
                        'second' => 'Мартин',
                        'third'  => 'Сесил',
                    ],
                    [
                        'first'  => 'Джон',
                        'second' => 'Доу',
                        'third'  => null,
                    ]
                ];

                $authorIds = [];

                // 2. Вставляем авторов в цикле и собираем их ID
                foreach ($authorsData as $data)
                {
                    // Метод create() возвращает объект модели со сгенерированным ID
                    //$author = Author::create($data);
                    
                    // firstOrCreate ищет запись по указанным полям. 
                    // Если не находит — создает новую.
                    $author = Author::firstOrCreate([
                        'first'  => $data['first'],
                        'second' => $data['second'],
                        'third'  => $data['third'],
                    ]);
                    $authorIds[] = $author->id;
                }

                // 3. Привязываем авторов к книге (Магия Eloquent)
                // Метод attach() автоматически заполнит таблицу books_authors
                // переданными ID авторов для этой конкретной книги
                //$book->authors()->attach($authorIds);

                // 3. Привязываем авторов к книге
                // Используем syncWithoutDetaching вместо attach. 
                // Это защитит от дубликатов в таблице books_authors, если связь уже была.
                $book->authors()->syncWithoutDetaching($authorIds);

                // 4. ТРИГГЕР ОБНОВЛЕНИЯ КОЛИЧЕСТВА КНИГ:
                // Перебираем привязанных авторов и увеличиваем их счетчик на 1
                foreach ($authorIds as $id)
                {
                    Author::where('id', $id)->increment('amountBooks');
                }

                return "Книга с ID {$book->id} и " . count($authorIds) . " автора успешно созданы!";
            });
        }
        catch(QueryException $e)
        {
            // Отлавливаем любые ошибки базы данных (MySQL)
            return response()->json([
                'success' => false,
                'error'   => 'Ошибка базы данных!',
                'details' => $e->getMessage() // В продакшене эту строку лучше убрать
            ], 500);
        }
        catch (\Exception $e)
        {
            // Отлавливаем любые другие ошибки PHP
            return response()->json([
                'success' => false,
                'error'   => 'Что-то пошло не так...',
            ], 500);
        }

        return response()->json(['message' => $result]);
    }
}
