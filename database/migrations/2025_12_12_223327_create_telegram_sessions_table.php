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
        Schema::create('telegram_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->bigInteger('telegram_user_id');
            $table->string('state')->default('browsing'); // browsing, cart, checkout, awaiting_contact, awaiting_comment
            $table->json('data')->nullable(); // cart items, contact info, etc
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamps();

            $table->unique(['shop_id', 'telegram_user_id']);
            $table->index('telegram_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_sessions');
    }
};
