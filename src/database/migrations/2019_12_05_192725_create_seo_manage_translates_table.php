<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoManageTranslatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('seo-manager.database.translates_table'), function(Blueprint $table) {
            $table->increments('id')->comment('PK');
            $table->unsignedInteger('route_id')->comment('Route ID');
            $table->string('locale', 4)->comment('Locale');
            $table->jsonb('title')->nullable()->comment('Title');
            $table->jsonb('meta_title')->nullable()->comment('Meta tag - Title');
            $table->jsonb('meta_description')->nullable()->comment('Meta tag - Description');
            $table->jsonb('meta_keywords')->nullable()->comment('Meta tag - Keywords');
            $table->jsonb('meta_url')->nullable()->comment('Meta tag - URL');
            $table->jsonb('meta_image')->nullable()->comment('Meta tag - Image URL');
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
        Schema::dropIfExists(config('seo-manager.database.translates_table'));
    }
}
