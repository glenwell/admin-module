<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use TCG\Voyager\Models\Page;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id');
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->text('image_meta')->nullable();
            $table->string('slug')->unique();
            $table->text('meta_description')->nullable();
            $table->text('focus_keywords')->nullable();
            $table->integer('seo_score')->default(0);
            $table->enum('status', Page::$statuses)->default(Page::STATUS_INACTIVE);
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
        Schema::drop('pages');
    }
}
