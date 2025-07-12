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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etudiant_id');
            $table->unsignedBigInteger('seance_id');
            $table->enum('statut', ['present', 'retard', 'absent']);
            $table->boolean('justifie')->default(false);
            $table->timestamps();

            $table->foreign('etudiant_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('seance_id')
                  ->references('id')
                  ->on('seances')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
