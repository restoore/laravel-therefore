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
            $table->integer('streamNo');
            $table->string('fileName');
            $table->integer('size');
            $table->integer('therefore_document_id')->unsigned();
            $table->foreign('therefore_document_id')->references('id')->on('therefore_documents')->onDelete('cascade');
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
