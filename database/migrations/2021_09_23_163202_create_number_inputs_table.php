<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNumberInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('number_inputs', function (Blueprint $table) {
            $table->id();
            $table->decimal("min")->nullable();
            $table->decimal("max")->nullable();
            $table->boolean("can_be_decimal")->nullable();
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
        Schema::dropIfExists('number_inputs');
    }
}
