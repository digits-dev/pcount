<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevisedQtyToCountLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('count_lines', function (Blueprint $table) {
            $table->integer('revised_qty', false, true)->default(0)->length(10)->unsigned()->after('qty')->nullable();
            $table->string('line_remarks',150)->after('line_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('count_lines', function (Blueprint $table) {
            $table->dropColumn('revised_qty');
            $table->dropColumn('line_remarks');
        });
    }
}
