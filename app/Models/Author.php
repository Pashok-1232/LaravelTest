<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use App\Traits\HasTranslit;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Table(timestamps: false)]
class Author extends Model
{
    use HasTranslit, HasFactory;

    //public $timestamps = false;

    // Убрали amountBooks из fillable, чтобы защитить от ручного ввода
    protected $fillable = ['first', 'second', 'third', 'translit', 'flags'];

    // Регистрируем «триггеры» (события Eloquent)
    protected static function booted(): void
    {
        // Событие ТРИГГЕР: Перед созданием записи (creating)
        static::creating(function (Author $author) {
            $author->autoFillFields();
        });
    }

    // Создаем виртуальное свойство full_name
    protected function fullName(): CastsAttribute
    {
        return CastsAttribute::get(function () {
            // Фильтруем массив, чтобы убрать пустые элементы, если какого-то имени нет
            return implode(' ', array_filter([
                $this->second,
                $this->first,
                $this->third,
            ]));
        });
    }

    /**
     * Обратная связь «Многие-ко-многим» с книгами
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(
            Book::class,
            'books_authors',
            'authorId',          // Теперь текущая модель Author, ключ в пивоте — authorId
            'bookId'             // Связываемая модель Book, ключ в пивоте — bookId
        );
    }

    public function getTranslitSource(): string
    {
        return implode(' ', array_filter([$this->second, $this->first, $this->third]));
    }
}
