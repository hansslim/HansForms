<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInputElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_elements', function (Blueprint $table) {
            $table->id();
            $table->string("header");
            $table->string("description")->nullable();
            $table->boolean("is_mandatory");
            //$table->json("special_rules")->nullable();
            $table->timestamps();

            //fk: form_element_id
            $table->foreignId('form_element_id')->unique()->constrained('form_elements')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('input_elements');
    }
}
