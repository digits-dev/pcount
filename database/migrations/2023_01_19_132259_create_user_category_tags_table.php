<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCategoryTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_category_tags', function (Blueprint $table) {
            $table->id();
            $table->string('user_name',20)->nullable();
            $table->string('category_tag_number',50)->nullable();
            $table->integer('warehouse_categories_id', false, true)->length(10)->unsigned()->nullable();
            $table->string('status', 10)->default('ACTIVE')->nullable();
            $table->integer('is_used', false, true)->default(0)->length(10)->unsigned()->nullable();
            $table->integer('created_by', false, true)->length(10)->unsigned()->nullable();
            $table->integer('updated_by', false, true)->length(10)->unsigned()->nullable();
            $table->integer('deleted_by', false, true)->length(10)->unsigned()->nullable();
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
        Schema::dropIfExists('user_category_tags');
    }
}
