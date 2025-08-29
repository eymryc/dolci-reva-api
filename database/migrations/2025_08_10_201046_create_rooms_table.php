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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_id')->constrained('addresses')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('max_guests')->default(1);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->enum('type', ['SIMPLE', 'DOUBLE', 'TWIN', 'TRIPLE', 'QUAD']);
            $table->enum('standing', ['STANDARD', 'DELUXE', 'EXÉCUTIVE', 'SUITE','SUITE JUNIOR','SUITE EXÉCUTIVE','SUITE PRÉSIDENTIELLE']);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
