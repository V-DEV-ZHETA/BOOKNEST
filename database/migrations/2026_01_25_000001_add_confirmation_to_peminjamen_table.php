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
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->enum('confirmation_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
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
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->dropColumn(['confirmation_status', 'confirmed_by', 'confirmed_at', 'confirmed_notes']);
        });
    }
};

