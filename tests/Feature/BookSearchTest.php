<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Repositories\DBSearchRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    // Активируем автоматическую очистку тестовой базы данных
    //use RefreshDatabase;

    use DatabaseMigrations;

    public function test_it_can_search_authors_by_name()
    {
        // 1. Подготовка данных (Представим, что мы настроили фабрики)
        Author::factory()->count(10)->create();

        $martin = Author::create([
            'first' => 'Роберт', 
            'second' => 'Мартин', 
            'third' => 'Сесил'
        ]);
        
        $john = Author::create([
            'first' => 'Джон', 
            'second' => 'Доу', 
            'third' => null
        ]);
        
        /*Author::factory()->create([
            'first' => 'Роберт', 
            'second' => 'Мартин', 
            'third' => 'Сесил'
        ]);
        Author::factory()->create([
            'first' => 'Джон', 
            'second' => 'Доу', 
            'third' => null
        ]);*/

        // 2. МОКАЕМ РЕПОЗИТОРИЙ: подменяем тяжелый FULLTEXT запрос к базе
        /*$this->mock(DBSearchRepository::class, function (MockInterface $mock) use ($martin) {
            // Ожидаем, что метод вызовется с аргументом 'Мартин' и вернет коллекцию с ID Мартина
            $mock->shouldReceive('findIdsByQuery')
                 ->with('Мартин')
                 ->andReturn(collect([$martin->id]));
        });*/

        // 3. Делаем запрос (Сервис вызовет наш подмененный мок, а не реальный SQL)
        $response = $this->get('/search?search=Мартин&type=authors');

        // 4. Проверки (Assertions)
        $response->assertStatus(200); // Проверяем, что страница открылась успешно
        $response->assertSee('Роберт Мартин'); // Видим целевого автора
        $response->assertDontSee('Джон Доу'); // Убеждаемся, что лишний автор отсеялся!
    }
}
