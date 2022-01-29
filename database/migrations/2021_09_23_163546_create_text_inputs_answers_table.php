<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextInputsAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_inputs_answers', function (Blueprint $table) {
            $table->id();
            $table->string("value", 1024)->nullable();
            $table->timestamps();

            //fk: form_completion_id, text_input_id
            $table->foreignId('form_completion_id')->constrained('form_completions')->onDelete('cascade');
            $table->foreignId('text_input_id')->constrained('text_inputs')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('text_inputs_answers');
    }
}
