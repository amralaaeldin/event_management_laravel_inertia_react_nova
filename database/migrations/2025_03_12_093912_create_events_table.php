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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('start_date_time'); // Start date and time in UTC
            $table->text('description');
            $table->integer('duration'); // Duration in minutes
            $table->string('location');
            $table->string('status')->default('draft');
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('waitlist_capacity');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
