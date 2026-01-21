<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evidence_id')->constrained('evidence')->onDelete('cascade');
            $table->smallInteger('value'); // -1 or +1
            $table->timestamps();
            
            $table->unique(['user_id', 'evidence_id']);
            $table->index('evidence_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
