<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectInputChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('select_input_choices', function (Blueprint $table) {
            $table->id();
            $table->string("text");
            $table->string("description")->nullable();
            $table->integer("hidden_label")->nullable();
            $table->integer("order");
            $table->timestamps();

            //fk: select_input_id
            $table->foreignId('select_input_id')->constrained('select_inputs');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('select_input_choices');
    }
}
