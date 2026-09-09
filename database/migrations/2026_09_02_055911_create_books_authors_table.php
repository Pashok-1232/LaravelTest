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
        Schema::create('books_authors', function (Blueprint $table) {
            $table->foreignId('bookId')->constrained('books')->onDelete('cascade');
            $table->foreignId('authorId')->constrained('authors')->onDelete('cascade');
            
            // Создаем составной первичный ключ
            $table->primary(['bookId', 'authorId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_authors');
    }
};
