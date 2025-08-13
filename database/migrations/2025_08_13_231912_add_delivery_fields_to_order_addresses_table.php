<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('longitude');
            $table->string('delivery_time')->nullable()->after('delivery_date');
            $table->text('order_note')->nullable()->after('delivery_time');
        });
    }

    public function down()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropColumn(['delivery_date', 'delivery_time', 'order_note']);
        });
    }

};
