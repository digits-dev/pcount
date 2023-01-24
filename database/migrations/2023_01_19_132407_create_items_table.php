<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('digits_code',10)->nullable();
            $table->string('upc_code',100)->nullable();
            $table->string('upc_code2',100)->nullable();
            $table->string('upc_code3',100)->nullable();
            $table->string('upc_code4',100)->nullable();
            $table->string('upc_code5',100)->nullable();
            $table->string('item_description',150)->nullable();
            $table->string('model',150)->nullable();
            $table->integer('brands_id', false, true)->length(10)->unsigned()->nullable();
            $table->integer('warehouse_categories_id', false, true)->length(10)->unsigned()->nullable();
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
        Schema::dropIfExists('items');
    }
}
