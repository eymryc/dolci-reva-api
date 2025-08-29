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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            // Clé étrangère
            $table->unsignedBigInteger('activity_id');

            // Horaires
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            // Capacité
            $table->unsignedInteger('max_participants');

            // Timestamps
            $table->timestamps();

            // Contrainte de clé étrangère
            $table->foreign('activity_id')
                ->references('id')
                ->on('activities')
                ->onDelete('cascade');

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
