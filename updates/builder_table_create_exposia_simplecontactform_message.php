<?php namespace Exposia\SimpleContactForm\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateExposiaSimplecontactformMessage extends Migration
{
    public function up()
    {
        Schema::create('exposia_simplecontactform_message', function($table)
        {
            $table->increments('id')->unsigned();
            $table->string('firstname', 255);
            $table->string('lastname', 255);
            $table->string('email', 255);
            $table->string('phone', 255);
            $table->string('street', 255);
            $table->string('city', 255);
            $table->string('state', 255);
            $table->string('zip', 255);
            $table->string('country', 255);
            $table->string('company', 255);
            $table->string('content', 1055);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exposia_simplecontactform_message');
    }
}
