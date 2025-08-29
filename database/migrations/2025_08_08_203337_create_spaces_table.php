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
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();

            // Clé étrangère
            $table->unsignedBigInteger('venue_id');
            
            // Informations de base
            $table->string('name');
            $table->enum('type', ['TABLE', 'SALON', 'PISTE']);
            $table->unsignedInteger('min_guests');
            $table->unsignedInteger('max_guests');
            $table->boolean('is_hourly_rate')->default(false);
            

            // Contrainte de clé étrangère
            $table->foreign('venue_id')
                  ->references('id')
                  ->on('venues')
                  ->onDelete('cascade');

            $table->softDeletes();


            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
