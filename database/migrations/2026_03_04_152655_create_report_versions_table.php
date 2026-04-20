<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('report_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->integer('version');
            $table->json('data_snapshot'); // snapshot completo do relatório
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->string('change_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['report_id', 'version']);
            $table->index('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_versions');
    }
};