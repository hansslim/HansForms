<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooleanInputsAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boolean_inputs_answers', function (Blueprint $table) {
            $table->id();
            $table->boolean("value")->nullable();
            $table->timestamps();

            //fk: form_completion_id, boolean_input_id
            $table->foreignId('form_completion_id')->constrained('form_completions')->onDelete('cascade');
            $table->foreignId('boolean_input_id')->constrained('boolean_inputs')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boolean_inputs_answers');
    }
}
