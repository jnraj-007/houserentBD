<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserverificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userverifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId');
            $table->text('image');
            $table->string('name');
            $table->bigInteger('nIdNumber');
            $table->text('frontNId');
            $table->text('backNId');
            $table->string('contact');
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('userverifications');
    }
}
