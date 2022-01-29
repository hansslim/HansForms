<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string("slug")->unique(); //type uuid
            $table->string("name", 128);
            $table->string("description", 512)->nullable();
            $table->dateTime("start_time");
            $table->dateTime("end_time");
            $table->boolean("has_public_results")->default(false);

            $table->boolean("has_private_token");
            $table->timestamps();

            //foreign key: user_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
    }
}
