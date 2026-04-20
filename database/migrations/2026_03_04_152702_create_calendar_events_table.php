<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('google_event_id');
            $table->string('google_calendar_id');
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('location', 500)->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->boolean('all_day')->default(false);
            $table->enum('status', ['confirmed', 'tentative', 'cancelled'])->default('confirmed');
            $table->string('color', 7)->nullable();
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['client_id', 'google_event_id']);
            $table->index(['client_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};