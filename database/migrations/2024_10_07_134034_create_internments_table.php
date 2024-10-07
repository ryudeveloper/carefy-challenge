<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('internments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('var_patient_id');
            $table->string('var_guia')->unique();
            $table->date('var_entrada');
            $table->date('var_saida')->nullable();
            $table->timestamps();

            $table->foreign('var_patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internments');
    }
};
