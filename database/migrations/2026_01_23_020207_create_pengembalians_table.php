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
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('inventori_buku_id')->nullable()->constrained('inventori_bukus')->nullOnDelete();
            $table->date('return_date');
            $table->enum('condition', ['good', 'damaged', 'damaged_heavy', 'lost'])->default('good');
            $table->text('notes')->nullable();
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->integer('late_days')->default(0);
            
            // Confirmation fields for self-service workflow
            $table->enum('confirmation_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('confirmed_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('confirmation_status');
            $table->index(['user_id', 'confirmation_status']);
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

