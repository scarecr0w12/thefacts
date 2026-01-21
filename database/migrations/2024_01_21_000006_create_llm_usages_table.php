<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('llm_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('provider')->default('openai');
            $table->string('model');
            $table->string('action')->index(); // claim_analysis, evidence_check, etc.
            $table->integer('input_tokens');
            $table->integer('output_tokens');
            $table->decimal('cost', 10, 6)->default(0);
            $table->integer('response_time_ms');
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['created_at']);
            $table->index(['provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('llm_usages');
    }
};
