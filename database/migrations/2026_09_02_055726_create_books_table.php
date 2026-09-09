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
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // Автоинкрементный primary key (BigInt по умолчанию в Laravel)
            $table->string('name', 255); // по умолчанию создаются как NOT NULL. ->nullable() разрешает
            $table->string('clearName', 255);
            $table->string('translit', 255)->unique();
            $table->tinyInteger('flags')->default(0);
            //$table->timestamps(); // Создает поля created_at и updated_at (стандарт Laravel)
            //В модели Eloquent: Laravel по умолчанию ожидает наличие этих полей. Чтобы он не выдавал ошибку при сохранении книг, в будущей модели Book нужно будет обязательно отключить их, добавив свойство:public $timestamps = false;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
