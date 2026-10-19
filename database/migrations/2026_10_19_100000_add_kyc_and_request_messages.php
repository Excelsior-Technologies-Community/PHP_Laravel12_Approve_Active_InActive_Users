<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add KYC Document fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_document_type')->nullable()->after('phone'); // e.g. National ID, Passport, Driving License
            $table->string('id_document_path')->nullable()->after('id_document_type');
            $table->enum('kyc_status', ['unsubmitted', 'pending', 'verified', 'rejected'])->default('unsubmitted')->after('id_document_path');
            $table->text('kyc_notes')->nullable()->after('kyc_status');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_notes');
            $table->foreignId('kyc_verified_by')->nullable()->constrained('users')->nullOnDelete()->after('kyc_verified_at');
        });

        // 2. Create request_messages table for two-way chat & attachments
        Schema::create('request_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamps();

            $table->index('request_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_messages');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kyc_verified_by']);
            $table->dropColumn([
                'id_document_type',
                'id_document_path',
                'kyc_status',
                'kyc_notes',
                'kyc_verified_at',
                'kyc_verified_by',
            ]);
        });
    }
};
