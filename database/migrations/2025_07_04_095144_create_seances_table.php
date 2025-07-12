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
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('enseignant_id');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->enum('type', ['presentiel', 'e-learning', 'workshop']);
            $table->string('classe');
            $table->boolean('annule')->default(false);
            $table->timestamps();

            $table->foreign('module_id')
                  ->references('id')
                  ->on('modules')
                  ->onDelete('cascade');

            $table->foreign('enseignant_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seances');
    }
};
