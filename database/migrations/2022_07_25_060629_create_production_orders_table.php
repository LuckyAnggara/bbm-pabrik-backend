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
            $table->string('pic_name');
            $table->string('customer_name');
            $table->text('notes');
            $table->enum('status', ['NEW ORDER', 'WORK IN PROGRESS', 'DONE', 'WAREHOUSE', 'SHIPPING'])->default('NEW ORDER');
            $table->dateTime('target_date');
            $table->dateTime('order_date');
            $table->integer('created_by')->nullable();
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
