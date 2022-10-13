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
        Schema::create('master_exit_items', function (Blueprint $table) {
            $table->id();
            $table->string('mutation_code')->unique();
            $table->string('type')->default('KELUAR');
            $table->string('no_pol')->nullable();
            $table->string('admin_name')->nullable();
            $table->string('driver_name')->nullable();
            $table->text('notes');
            $table->integer('created_by');
            $table->dateTime('data_date');
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
        Schema::dropIfExists('master_exit_items');
    }
};
