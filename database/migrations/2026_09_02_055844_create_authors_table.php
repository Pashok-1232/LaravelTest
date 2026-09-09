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
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('first', 100)->nullable();  // Имя
            $table->string('second', 100);             // Фамилия
            $table->string('third', 100)->nullable();  // Отчество (nullable, если нет)
            $table->string('translit', 255)->unique();
            $table->integer('amountBooks')->default(0);
            $table->tinyInteger('flags')->default(0);
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
