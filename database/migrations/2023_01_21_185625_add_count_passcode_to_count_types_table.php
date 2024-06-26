<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountPasscodeToCountTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('count_types', function (Blueprint $table) {
            $table->string('count_passcode',150)->after('count_type_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('count_types', function (Blueprint $table) {
            $table->dropColumn('count_passcode');
        });
    }
}
