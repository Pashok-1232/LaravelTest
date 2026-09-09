<?php

namespace App\Models;

use App\Traits\HasTranslit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasTranslit;

    // Отключаем timestamps, так как мы удалили их из миграции
    public $timestamps = false;

    // Разрешаем массовое заполнение этих полей (безопасность Laravel)
    protected $fillable = ['name', 'clearName', 'translit', 'flags'];

    // Регистрируем «триггеры» (события Eloquent)
    protected static function booted(): void
    {

        // Событие ТРИГГЕР: Перед созданием записи (creating)
        static::creating(function (Book $book) {
            $book->autoFillFields();

            // Ищем, есть ли уже книга с таким же translit
            $existingBook = Book::where('translit', $book->translit)->first();
            
            if ($existingBook)
            {
                // Если нашли — модифицируем ТЕКУЩИЙ translit старой книги, добавив её ID
                // (Пример: было 'chistyi-kod', станет 'chistyi-kod-12')
                $existingBook->translit = $existingBook->translit . '-' . $existingBook->id;
                $existingBook->save();
            }
        });

        // ТРИГГЕР: После того, как авторы привязались к книге
        static::created(function (Book $book) {
            // Мы сделаем инкремент в контроллере или через связь, 
            // но так как при самом создании книги связь еще пуста (attach вызывается позже),
            // безопаснее всего управлять счетчиком в момент вызова attach().
        });
    }

    /**
     * Связь «Многие-ко-многим» с авторами
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(
            Author::class,       // С какой моделью связываем
            'books_authors',     // Имя промежуточной таблицы
            'bookId',            // Внешний ключ этой модели (Book) в пивоте
            'authorId'           // Внешний ключ связываемой модели (Author) в пивоте
        );
    }

    // Первый аргумент Laravel передает автоматически — это объект Query Builder
    // Второй аргумент ($search) — это строка, которую мы передадим из контроллера
    public function scopeSearchByName(Builder $query, ?string $search): Builder
    {
        // Если строка поиска пустая, возвращаем запрос как есть
        if (blank($search))
            return $query;

        // Выполняем SQL-фильтрацию LIKE
        return $query->where('name', 'like', '%'.$search.'%');
    }

    public function scopeFilterByType(Builder $query, ?string $type, string $search): Builder
    {
        if (blank($type))
            return $query;

        // Если тип 'books', мы уже ищем по имени в первом скоупе,
        // но если тип 'authors', можем отфильтровать книги по авторам через связь
        if ($type === 'authors')
        {
            return $query->whereHas('authors', function ($q) use ($search){
                // Предположим, у автора поле называется 'name' Запрос вернет книги, у авторов которых имя совпадает с поиском
                // (Для этого можно передать поисковую строку)
                $q->where('first', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function scopeFilterBySearchType(Builder $query, string $search, ?string $type): Builder
    {
        if (blank($type))
            return $query;
        
        // Если тип равен 'authors', то ищем строго по авторам через EXISTS-запрос
        if ($type === 'authors')
        {
            return $query->whereHas('authors', function (Builder $authorQuery) use ($search) {
                // Здесь мы уже в контексте таблицы авторов. Предположим, у автора имя хранится в поле 'name' (или 'fio')
                $authorQuery->where('first', 'like', '%'.$search.'%');
            });
        }

        // По умолчанию (если тип 'books' или не указан) — ищем просто по названию книги
        return $query->where('name', 'like', '%'.$search.'%');
    }

}
