<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('company_name');
            $table->string('trade_name')->nullable();
            $table->string('cnpj', 18)->nullable()->unique();
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->json('address')->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->boolean('show_roi')->default(false);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('uuid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};