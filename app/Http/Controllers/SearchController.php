<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\BookUrlGenerator;
use App\Services\BookSearchService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SearchController extends Controller
{
    public function index(Request $request, BookUrlGenerator $urlGenerator, BookSearchService $searchService)
    {
        /*
        use Illuminate\Support\Facades\Validator;
        use Illuminate\Validation\Rule;

        Validator::make($data, [
            'zones' => [
                'required',
                Rule::in(['first-zone', 'second-zone']),
            ],
        ]);
        */
        $rules = [
            'search' => 'required|string',
            'type' => [
                Rule::in('authors', 'books', 'genres')
            ],
        ];

        $validatedData = $request->validate($rules);
        
        //Это как пример рсширения. Он работать не будет конкретно тут (вначале ищет по названию книги Джон, а потом по авторам)
        /*$books = Book::searchByName($validatedData['search'])
            ->filterByType($validatedData['type'] ?? null, $validatedData['search'])
            ->with('authors')
            ->paginate(5);*/
        
        // Рабочий пример, но по жёскому условию
        /*$books = Book::filterBySearchType($validatedData['search'], $validatedData['type'] ?? null)
            ->with('authors')
            ->paginate(5);*/

        // Рабочий пример через fulltext таблицу для поиска
        // Контроллер ничего не знает про фасад DB или sql-логику
        $searchResult = $searchService->searchAll($validatedData);
        $searchRow = $validatedData['search'];
        $type = $validatedData['type'] ?? null;
        return view('books.search', compact('searchResult', 'urlGenerator', 'searchRow', 'type'));
    }
}
