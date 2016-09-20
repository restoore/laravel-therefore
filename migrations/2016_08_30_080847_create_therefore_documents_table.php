<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThereforeDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('therefore_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('docNo');
            $table->string('categoryNo');
            $table->mediumText('ctgryName');
            $table->string('lastChangeTime');
            $table->string('title');
            $table->string('versionNo');
            $table->string('searchableField');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('therefore_documents');
    }
}
