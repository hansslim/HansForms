<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNumberInputsAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('number_inputs_answers', function (Blueprint $table) {
            $table->id();
            $table->decimal("value")->nullable();
            $table->timestamps();

            //fk: form_completion_id, number_input_id
            $table->foreignId('form_completion_id')->constrained('form_completions');
            $table->foreignId('number_input_id')->constrained('number_inputs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('number_inputs_answers');
    }
}
