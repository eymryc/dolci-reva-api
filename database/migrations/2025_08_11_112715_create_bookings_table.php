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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Clé étrangère vers l'utilisateur
            // $table->foreignId('user_id')->constrained('users');

            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade'); // Celui qui fait la réservation
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Celui qui possède l'élément réservé

            // Relation polymorphique
            // $table->string('item_type'); // 'PROPERTY', 'ROOM', 'SPACE', 'ACTIVITY'
            // $table->unsignedBigInteger('item_id');
            $table->morphs('bookable');


            // Période de réservation
            $table->dateTime('start_date');
            $table->dateTime('end_date');

            // Informations financières
            $table->decimal('total_price', 10, 2);
            $table->decimal('commission_amount', 10, 2)->nullable();
            $table->decimal('owner_amount', 10, 2)->nullable();

            // Statuts
            $table->enum('status', [
                'CONFIRME',
                'ANNULE',
                'EN_ATTENTE'
            ])->default('EN_ATTENTE');

            $table->enum('payment_status', [
                'PAYE',
                'REMBOURSE',
                'EN_ATTENTE'
            ])->default('EN_ATTENTE');

            // Informations supplémentaires
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();



            // Index
            $table->index('start_date');
            $table->index('end_date');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
