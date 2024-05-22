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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('sequence');
            $table->integer('shift')->nullable();
            $table->string('pic_name');
            $table->string('customer_name');
            $table->text('notes');
            $table->integer('jenis_hasil');
            $table->enum('status', ['NEW ORDER', 'WORK IN PROGRESS', 'DONE PRODUCTION', 'WAREHOUSE', 'SHIPPING', 'RETUR', 'RECEIVE'])->default('NEW ORDER');
            $table->dateTime('target_date');
            $table->dateTime('order_date');
            $table->string('shipping_id')->nullable();
            $table->integer('created_by');
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
        Schema::dropIfExists('production_orders');
    }
};
