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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('postal_code', 20)->nullable()->after('city');
            $table->date('birth_date')->nullable()->after('postal_code');
            $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('gender');
            $table->date('member_since')->nullable()->after('status');
            $table->date('member_until')->nullable()->after('member_since');
            $table->string('member_number', 50)->nullable()->after('member_until');
            $table->string('avatar')->nullable()->after('member_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'city',
                'postal_code',
                'birth_date',
                'gender',
                'status',
                'member_since',
                'member_until',
                'member_number',
                'avatar',
            ]);
        });
    }
};

