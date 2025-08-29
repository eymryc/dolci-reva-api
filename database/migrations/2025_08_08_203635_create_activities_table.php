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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            // Clés étrangères
            $table->unsignedBigInteger('organizer_id');
            // $table->unsignedBigInteger('address_id');

            // Informations de base
            $table->string('title');
            $table->text('description');

            // Type et durée
            $table->enum('type', ['RANDO', 'VISITE', 'ATELIER']);
            $table->unsignedInteger('duration_minutes');

            // Équipement et prix
            $table->boolean('equipment_provided')->default(false);
            $table->decimal('price_per_person', 10, 2);


            // Adresse
            $table->text('address');
            $table->text('state');
            $table->text('street')->nullable();
            $table->string('city');
            $table->string('country');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Timestamps
            $table->timestamps();

            // Contraintes de clés étrangères
            $table->foreign('organizer_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // $table->foreign('address_id')
            //     ->references('id')
            //     ->on('addresses')
            //     ->onDelete('cascade');

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
