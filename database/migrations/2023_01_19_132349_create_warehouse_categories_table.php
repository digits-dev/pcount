<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_categories', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse_category_code',50)->nullable();
            $table->string('warehouse_category_description',150)->nullable();
            $table->string('status', 10)->default('ACTIVE')->nullable();
            $table->integer('is_restricted', false, true)->default(0)->length(10)->unsigned()->nullable();
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
        Schema::dropIfExists('warehouse_categories');
    }
}
