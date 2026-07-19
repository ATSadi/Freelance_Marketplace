<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('bank');
            $table->string('account_name');
            $table->string('bank_name');
            $table->text('account_number');
            $table->text('routing_number')->nullable();
            $table->string('account_last_four', 4);
            $table->string('country', 2)->default('US');
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });

        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payout_method_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
        Schema::dropIfExists('payout_methods');
    }
};
