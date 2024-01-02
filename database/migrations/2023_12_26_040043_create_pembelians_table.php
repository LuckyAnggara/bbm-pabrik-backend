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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_faktur');
            $table->string('nama_supplier')->nullable();
            $table->double('sub_total')->default(0);
            $table->double('pajak')->default(0);
            $table->double('ongkir')->default(0);
            $table->double('diskon')->default(0);
            $table->double('total')->default(0);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('pembelians');
    }
};
