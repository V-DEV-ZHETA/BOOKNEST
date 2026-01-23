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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['peminjaman', 'pengembalian', 'keterlambatan', 'kerusakan', 'kehilangan', 'inventaris', 'anggota', 'denda', 'statistik', 'lainnya']);
            $table->text('content');
            $table->date('generated_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('period_type', 50)->nullable();
            $table->text('summary')->nullable();
            $table->integer('total_records')->default(0);
            $table->integer('page_count')->default(0);
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
