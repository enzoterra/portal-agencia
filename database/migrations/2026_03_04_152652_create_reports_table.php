<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->date('reference_month');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');

            // ── Resumo e metas ────────────────────────────────
            $table->text('summary')->nullable();
            $table->text('next_month_goals')->nullable();

            // ── Tráfego Pago ──────────────────────────────────
            $table->decimal('investment', 12, 2)->default(0);  // Valor investido
            $table->decimal('revenue', 12, 2)->default(0);     // Receita gerada
            $table->integer('paid_conversations')->nullable();  // Qtd conversas
            $table->decimal('cps', 8, 2)->nullable();          // Custo por conversa
            $table->decimal('cpc', 8, 2)->nullable();          // Custo por clique

            // ── ROI (gerado pelo MySQL) ────────────────────────
            $table->decimal('roi', 8, 2)->storedAs(
                '((revenue - investment) / NULLIF(investment, 0)) * 100'
            );

            // ── Instagram ─────────────────────────────────────
            $table->integer('ig_publications')->nullable();    // Nº publicações
            $table->integer('ig_interactions')->nullable();    // Interações
            $table->string('ig_reach')->nullable();            // Alcance (ex: "56k")
            $table->integer('ig_new_followers')->nullable();   // Seguidores novos
            $table->integer('ig_views')->nullable();           // Visualizações
            $table->integer('ig_profile_visits')->nullable();  // Visitas ao perfil

            // ── Top Conteúdos (JSON: array de até 3 itens) ────
            // Estrutura: [{ "title": "", "description": "", "url": "" }]
            $table->json('top_contents')->nullable();

            // ── Nosso Público ─────────────────────────────────
            // Localizações: [{ "city": "Dourados", "percentage": 8 }] (top 5)
            $table->json('audience_locations')->nullable();

            // Faixa etária: { "13-17": 5, "18-24": 30, "25-34": 40, "35-44": 15, "45-54": 7, "55+": 3 }
            $table->json('audience_age')->nullable();

            // Gênero: { "male": 45, "female": 55 }
            $table->json('audience_gender')->nullable();

            // ── Controle ──────────────────────────────────────
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('current_version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['client_id', 'reference_month']);
            $table->index('status');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};