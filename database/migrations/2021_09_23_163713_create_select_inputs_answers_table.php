<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectInputsAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('select_inputs_answers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            //fk: form_completion_id, select_input_id, select_choice_id
            $table->foreignId('form_completion_id')->constrained('form_completions');
            $table->foreignId('select_input_id')->constrained('select_inputs');
            $table->foreignId('select_choice_id')->nullable()->constrained('select_input_choices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('select_inputs_answers');
    }
}
