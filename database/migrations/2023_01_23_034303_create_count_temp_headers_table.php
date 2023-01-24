<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountTempHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('count_temp_headers', function (Blueprint $table) {
            $table->id();
            $table->integer('count_types_id', false, true)->length(10)->unsigned()->nullable();
            $table->string('category_tag_number',50)->nullable();
            $table->integer('warehouse_categories_id', false, true)->length(10)->unsigned()->nullable();
            $table->integer('total_qty', false, true)->length(10)->unsigned()->nullable();
            $table->integer('created_by', false, true)->length(10)->unsigned()->nullable();
            $table->integer('updated_by', false, true)->length(10)->unsigned()->nullable();
            $table->integer('deleted_by', false, true)->length(10)->unsigned()->nullable();
            $table->integer('audited_by', false, true)->length(10)->unsigned()->nullable();
            $table->dateTime('audited_at')->nullable();
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
        Schema::dropIfExists('count_temp_headers');
    }
}
