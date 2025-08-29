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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            // Clés étrangères
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');

            // Informations de base
            $table->string('name', 255);
            $table->text('description');

            // Adresse
            $table->text('address');
            $table->text('state');
            $table->text('street')->nullable();
            $table->string('city');
            $table->string('country');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            // Capacité
            $table->unsignedInteger('max_guests')->nullable();
            $table->unsignedInteger('bedrooms')->nullable();
            $table->unsignedInteger('bathrooms')->nullable();
            $table->unsignedInteger('piece_number')->nullable();

            // Tarification
            $table->decimal('price', 10, 2)->default(0.00);

            // $table->string('tagline', 255)->nullable();
            // $table->string('legal_name', 255)->nullable();
            // $table->string('brand', 255)->nullable();

            // Types
            $table->enum('type', ['STUDIO','APPARTEMENT', 'VILLA', 'DUPLEX','TRIPLEX']);
            $table->enum('rental_type', ['ENTIER', 'COLOCATION']);

            // Timestamps
            $table->timestamps();

            // Soft delete
            $table->softDeletes();

            // Index
            $table->index('owner_id');
            // $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
