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
        Schema::table('milestones', function (Blueprint $table) {
            $table->text('submission_notes')->nullable()->after('status');
            $table->text('client_feedback')->nullable()->after('submission_notes');
            $table->timestamp('started_at')->nullable()->after('client_feedback');
            $table->timestamp('submitted_at')->nullable()->after('started_at');
            $table->timestamp('approved_at')->nullable()->after('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn([
                'submission_notes',
                'client_feedback',
                'started_at',
                'submitted_at',
                'approved_at',
            ]);
        });
    }
};
