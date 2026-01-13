<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['admin', 'customer'])->default('customer');
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->boolean('is_approved')->default(false);
            $table->string('phone')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('last_login')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_type',
                'status',
                'is_approved',
                'phone',
                'approved_at',
                'last_login'
            ]);
        });
    }
};