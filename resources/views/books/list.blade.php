<!-- resources/views/books/index.blade.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список книг</title>
    <style>
        .pagination { display: flex; list-style: none; padding: 0; }
        .pagination li { margin-right: 10px; }
        .book-item { margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>Каталог книг</h1>
    <form action="{{ route('search') }}" method="GET">
        <input type="text" name="search" value="{{ old('type') == 'search' ? old('search') : '' }}">
        <input type="submit" value="Искать!">
    </form>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($books->isEmpty())
        <p>Книг пока нет в базе данных.</p>
    @else
        <div class="books-list">
            @foreach($books as $book)
                <div class="book-item">
                    <h2>
                        <a href="{{ route('books.show', ['allPath' => $urlGenerator->makePath($book)]) }}">
                            {{ $book->name }}
                        </a>
                    </h2>

                    <p><strong>Авторы:</strong>
                        @if($book->authors->isNotEmpty())
                            @foreach($book->authors as $author)
                                <a href="/authors/{{ $author->translit }}">
                                    {{ trim("$author->first $author->second $author->third") }}
                                </a>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        @else
                            не указаны
                        @endif
                    </p>
                </div>
            @endforeach
        </div>

        <!-- Магия Laravel: одной строчкой выводим HTML-код пагинации (вперед, назад, номера страниц) -->
        <div class="pagination-links">
            {{ $books->links() }}
        </div>
    @endif
</body>
</html>
