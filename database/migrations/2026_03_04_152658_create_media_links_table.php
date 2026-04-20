<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_links', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url', 2048);
            $table->enum('type', ['folder', 'file', 'video', 'image', 'document', 'other'])->default('other');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('thumbnail_url', 2048)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_links');
    }
};