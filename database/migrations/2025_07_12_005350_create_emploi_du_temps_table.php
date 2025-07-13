<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emploi_du_temps', function (Blueprint $table) {
            $table->id();
            $table->string('jour_semaine'); // ex: Lundi, Mardi
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('enseignant_id')->nullable();
            $table->string('type')->nullable(); // ex: cours, workshop, e-learning
            $table->string('salle')->nullable();
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('enseignant_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emploi_du_temps');
    }
};
