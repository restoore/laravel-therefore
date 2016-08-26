<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThereforeFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('therefore_files', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('categoryNo');
            $table->integer('docNo');
            $table->integer('streamNo');
            $table->integer('versionNo');
            $table->string('fileName');
            $table->string('searchableField');
            $table->integer('size');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('therefore_files');
    }
}
