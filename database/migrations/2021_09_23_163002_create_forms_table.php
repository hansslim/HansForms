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
            $table->uuid("slug")->unique();
            $table->string("name");
            $table->string("description")->nullable();
            $table->dateTimeTz("start_time")->nullable();
            $table->dateTimeTz("end_time")->nullable();
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
