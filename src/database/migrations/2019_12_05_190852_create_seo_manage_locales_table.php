<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateSeoManageLocalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('seo-manager.database.locales_table'), function(Blueprint $table) {
            $table->increments('id')->comment('PK');
            $table->string('name')->default('')->comment('Locale code ex)en, ko, ...');
            $table->timestamps();
        });

        DB::table(config('seo-manager.database.locales_table'))->insert([
            'name' => config('seo-manager.locale'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('seo-manager.database.locales_table'));
    }
}
