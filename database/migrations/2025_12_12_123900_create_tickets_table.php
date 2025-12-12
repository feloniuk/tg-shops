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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->enum('status', [
                'open', 
                'in_progress', 
                'resolved', 
                'closed'
            ])->default('open');
            $table->enum('priority', [
                'low', 
                'medium', 
                'high', 
                'critical'
            ])->default('low');
            $table->timestamp('last_response_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
