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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', [
                'PETIT_DEJEUNER',
                'DEJEUNER',
                'DINER',
                'BOISSONS',
                'ENFANTS'
            ]);
            $table->boolean('is_active')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
