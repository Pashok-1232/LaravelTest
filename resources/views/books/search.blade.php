<!-- resources/views/books/index.blade.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результ поиска по {{ $searchRow }}</title>
    <style>
        .pagination { display: flex; list-style: none; padding: 0; }
        .pagination li { margin-right: 10px; }
        .book-item { margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #ccc; }
    </style>
</head>
<body>
    {{-- {{ $books->appends(request()->query())->links() }} --}}
    <a href="{{ request()->fullUrlWithQuery(['type'=>'books']) }}" @if(!isset($type) || $type == 'books') style='color:red;' @endif>Книги</a>
    <a href="{{ request()->fullUrlWithQuery(['type'=>'authors']) }}" @if(isset($type) && $type == 'authors') style='color:red;' @endif>Авторы</a>

    <h1>Результат поиска по </h1>
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

    @if($searchResult->isEmpty())
        <p>Ничего не найдено.</p>
    @else
        <div class="books-list">
            @foreach($searchResult as $item)
                @if ($item instanceof \App\Models\Book)
                    <div class="book-item">
                        <h2>
                            <a href="{{ route('books.show', ['allPath' => $urlGenerator->makePath($item)]) }}">
                                {{ $item->name }}
                            </a>
                        </h2>

                        <p><strong>Авторы:</strong>
                            @if($item->authors->isNotEmpty())
                                @foreach($item->authors as $author)
                                    <a href="/authors/{{ $author->translit }}">
                                        {{ trim("$author->first $author->second $author->third") }}
                                    </a>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            @else
                                не указаны
                            @endif
                        </p>
                    </div>
                @elseif ($item instanceof \App\Models\Author)
                    <div class="author-item">
                        <h3>Автор: {{ trim("$item->first $item->second $item->third") }}</h3>
                        <p>Всего книг в базе: {{ $item->amountBooks }}</p>
                        <a href="/authors/{{ $item->translit }}">Перейти к профилю</a>
                    </div>
                @endif
            @endforeach
            <div class="pagination-links">
                {{ $searchResult->links() }}
            </div>
        </div>
    @endif
</body>
</html>
