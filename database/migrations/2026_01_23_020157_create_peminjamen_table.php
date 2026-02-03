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
        Schema::create('peminjamen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('inventori_buku_id')->constrained('inventori_bukus')->onDelete('cascade');
            $table->date('borrow_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'lost'])->default('borrowed');
            $table->text('notes')->nullable();
            
            // Confirmation fields for self-service workflow
            $table->enum('confirmation_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('confirmed_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('confirmation_status');
            $table->index(['user_id', 'confirmation_status']);
            $table->index(['status', 'confirmation_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};

