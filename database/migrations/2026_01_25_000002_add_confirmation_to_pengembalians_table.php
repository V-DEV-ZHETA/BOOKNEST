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
        Schema::table('pengembalians', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('peminjaman_id');
            $table->unsignedBigInteger('inventori_buku_id')->nullable()->after('user_id');
            $table->enum('confirmation_status', ['pending', 'approved', 'rejected'])->default('pending')->after('fine_amount');
            $table->unsignedBigInteger('confirmed_by')->nullable()->after('confirmation_status');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
            $table->text('confirmed_notes')->nullable()->after('confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalians', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'inventori_buku_id', 'confirmation_status', 'confirmed_by', 'confirmed_at', 'confirmed_notes']);
        });
    }
};

