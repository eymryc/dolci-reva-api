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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();

            // Clés étrangères
            $table->unsignedBigInteger('owner_id');
            // $table->unsignedBigInteger('address_id');
            $table->unsignedBigInteger('category_id');

            // Informations de base
            $table->string('name');
            $table->text('description')->nullable();

            // Adresse
            $table->text('address');
            $table->text('state');
            $table->text('street')->nullable();
            $table->string('city');
            $table->string('country');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Type et capacité
            $table->enum('type', ['RESTAURANT', 'BAR', 'LOUNGE', 'SALLE_EVENT']);
            $table->unsignedInteger('capacity');

            // Contraintes de clés étrangères
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // $table->foreign('address_id')
            //     ->references('id')
            //     ->on('addresses')
            //     ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict');
            $table->softDeletes();
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
