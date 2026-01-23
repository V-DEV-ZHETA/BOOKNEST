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
        Schema::create('inventori_bukus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->unique();
            $table->integer('quantity')->default(1);
            $table->text('description')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('year')->nullable();
            $table->string('edition')->nullable();
            $table->string('language')->nullable();
            $table->integer('pages')->nullable();
            $table->string('category')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventori_bukus');
    }
};
