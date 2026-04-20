<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->enum('payment_method', ['pix', 'bank_transfer', 'credit_card', 'other'])->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue', 'under_review', 'cancelled'])->default('pending');
            $table->text('pix_qr_code')->nullable();
            $table->string('pix_key')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_id');
            $table->index('status');
            $table->index('due_date');
            $table->index(['client_id', 'status']); // índice composto para dashboard
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};