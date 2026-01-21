<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidence', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('claim_id')->constrained('claims')->onDelete('cascade');
            $table->text('url');
            $table->string('title')->nullable();
            $table->string('publisher_domain')->nullable()->index();
            $table->timestamp('published_at')->nullable();
            $table->enum('stance', ['SUPPORTS', 'REFUTES', 'CONTEXT'])->index();
            $table->text('excerpt');
            $table->enum('status', ['PENDING', 'READY', 'FAILED'])->default('PENDING');
            $table->text('error')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index('created_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence');
    }
};
