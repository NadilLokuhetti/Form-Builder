<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->string('type'); // text, textarea, checkbox, radio
            $table->string('label');
            $table->boolean('required')->default(false);
            $table->json('options')->nullable(); // For checkbox and radio fields
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fields');
    }
}