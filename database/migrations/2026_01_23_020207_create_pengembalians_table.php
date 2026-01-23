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
        Schema::create('pengembalians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamen')->onDelete('cascade');
            $table->date('return_date');
            $table->enum('condition', ['good', 'damaged', 'damaged_heavy', 'lost'])->default('good');
            $table->text('notes')->nullable();
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->string('received_by')->nullable();
            $table->string('checked_by')->nullable();
            $table->integer('late_days')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalians');
    }
};
