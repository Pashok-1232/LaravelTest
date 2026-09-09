<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books_searches', function (Blueprint $table) {
            $table->unsignedBigInteger('bookId');
            $table->string('search', 2048);

            // Явно связываем внешний ключ, указывая кастомную колонку и таблицу
            $table->foreign('bookId')
                ->references('id')
                ->on('books')
                ->onDelete('cascade');

            // Создаем составной полнотекстовый индекс
            $table->fullText('search'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_searches');
    }
};
