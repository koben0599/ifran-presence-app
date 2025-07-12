<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emploi_du_temps', function (Blueprint $table) {
            $table->string('classe');
            $table->foreignId('module_id')->constrained('modules');
            $table->foreignId('enseignant_id')->constrained('users');
            $table->integer('jour_semaine'); // 1=Lundi, 2=Mardi, etc.
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->enum('type_cours', ['presentiel', 'e-learning', 'workshop']);
            $table->string('salle')->nullable();
            $table->boolean('est_actif')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emploi_du_temps', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropForeign(['enseignant_id']);
            $table->dropColumn(['classe', 'module_id', 'enseignant_id', 'jour_semaine', 'heure_debut', 'heure_fin', 'type_cours', 'salle', 'est_actif']);
        });
    }
};
