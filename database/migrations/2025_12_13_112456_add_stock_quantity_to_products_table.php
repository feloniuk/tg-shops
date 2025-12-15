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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->after('price'); // Количество на складе
            $table->boolean('track_stock')->default(true)->after('stock_quantity'); // Отслеживать ли остатки
            $table->boolean('allow_backorder')->default(false)->after('track_stock'); // Разрешить заказ при нулевом остатке
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'track_stock', 'allow_backorder']);
        });
    }
};
