<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoManageRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('seo-manager.database.routes_table'), function(Blueprint $table) {
            $table->increments('id')->comment('PK');
            $table->string('uri')->default('')->comment('URI');
            $table->jsonb('params')->nullable()->comment('Parameters : array');
            $table->jsonb('mapping')->nullable()->comment('Parameters mapping data : object');
            $table->unsignedTinyInteger('ord')->default(1)->comment('Order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('seo-manager.database.routes_table'));
    }
}
