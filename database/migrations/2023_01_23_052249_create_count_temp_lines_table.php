<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountTempLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('count_temp_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('count_temp_headers_id', false, true)->length(10)->unsigned()->nullable();
            $table->string('item_code',60)->nullable();
            $table->integer('qty', false, true)->default(0)->length(10)->unsigned()->nullable();
            $table->integer('revised_qty', false, true)->default(0)->length(10)->unsigned()->nullable();
            $table->string('line_color',30)->nullable();
            $table->string('line_remarks',150)->nullable();
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
        Schema::dropIfExists('count_temp_lines');
    }
}
