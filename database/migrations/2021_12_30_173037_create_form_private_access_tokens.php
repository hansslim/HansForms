<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormPrivateAccessTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_private_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('was_used')->default(false);
            $table->string('token')->unique();
            $table->string('email');

            //foreign key: form_id
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_private_access_tokens');
    }
}
