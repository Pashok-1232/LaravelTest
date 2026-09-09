<!-- resources/views/books/book.blade.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $book->name }}</title>
</head>
<body>
    <h1>{{ $book->name }}</h1>

    <p><strong>Авторы:</strong> 
        @if($book->authors->isNotEmpty())
            @foreach($book->authors as $author)
                <a href="/authors/{{ $author->translit }}" class="author-link">
                    {{ $author->full_name }}
                </a>{{ !$loop->last ? ', ' : '' }}
            @endforeach
        @else
            Автор не указан
        @endif
    </p>

    <div class="book-description">
        <!-- Предположим, есть поле описания -->
        {{ $book->description ?? 'Описание отсутствует.' }} 
    </div>

    <a href="/books">Назад к списку книг</a>
</body>
</html>
