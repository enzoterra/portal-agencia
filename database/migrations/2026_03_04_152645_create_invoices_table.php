<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 50);
            $table->decimal('amount', 10, 2);
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('file_path', 500)->nullable(); // storage/app/private/
            $table->text('description')->nullable();
            $table->date('reference_month')->nullable(); // primeiro dia do mês
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['invoice_number', 'client_id']);
            $table->index('client_id');
            $table->index('reference_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};