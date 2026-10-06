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
    Schema::create('buses', function (Blueprint $table) {
    $table->id();

    $table->string('bus_number')->unique();
    $table->string('route');
    $table->string('status')->default('On Time');

    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();

    $table->timestamp('location_updated_at')->nullable();

    $table->timestamps();
    });
 }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
