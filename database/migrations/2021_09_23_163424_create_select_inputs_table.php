<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('select_inputs', function (Blueprint $table) {
            $table->id();
            $table->boolean("is_multiselect");
            $table->integer("min_amount_of_answers")->nullable();
            $table->integer("max_amount_of_answers")->nullable();
            $table->integer("strict_amount_of_answers")->nullable();
            $table->timestamps();

            //fk: input_element_id
            $table->foreignId('input_element_id')->unique()->constrained('input_elements');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('select_inputs');
    }
}
