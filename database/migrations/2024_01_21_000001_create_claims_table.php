<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('text', 280);
            $table->string('normalized_text', 280)->index();
            $table->string('context_url')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('verdict', ['UNVERIFIED', 'TRUE', 'FALSE', 'MIXED', 'MISLEADING', 'UNVERIFIABLE'])->default('UNVERIFIED');
            $table->unsignedTinyInteger('confidence')->default(0);
            $table->timestamps();
            
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
