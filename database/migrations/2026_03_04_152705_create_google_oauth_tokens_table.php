<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('google_oauth_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('access_token');  // criptografado via encrypted cast
            $table->text('refresh_token')->nullable(); // criptografado
            $table->string('token_type', 50)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('scopes')->nullable();
            $table->timestamps();

            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_oauth_tokens');
    }
};