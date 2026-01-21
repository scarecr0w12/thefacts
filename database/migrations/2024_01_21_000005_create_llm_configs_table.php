<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('llm_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('openai'); // openai, anthropic, etc.
            $table->text('api_key')->nullable();
            $table->string('model')->default('gpt-4-turbo'); // model name
            $table->boolean('enabled')->default(true);
            $table->integer('max_tokens')->default(1000);
            $table->decimal('temperature', 3, 2)->default(0.7);
            $table->text('system_prompt')->nullable();
            $table->decimal('cost_per_1k_tokens', 8, 6)->default(0.01);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('llm_configs');
    }
};
