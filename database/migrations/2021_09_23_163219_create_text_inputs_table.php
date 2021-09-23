<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_inputs', function (Blueprint $table) {
            $table->id();
            $table->integer("min_length")->nullable();
            $table->integer("max_length")->nullable();
            $table->integer("strict_length")->nullable();
            $table->timestamps();

            //fk: form_input_id
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('text_inputs');
    }
}
