<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->string('master_exit_item_id')->nullable();
            $table->boolean('is_po')->default(0);
            $table->string('production_order_id')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('police_number')->nullable();
            $table->string('man_power_name')->nullable();
            $table->dateTime('shipping_date')->nullable();
            $table->dateTime('receiving_date')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('proof')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shippings');
    }
};
