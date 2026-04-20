<?php

namespace App\Domain\Report\Services;

use App\Domain\Report\Models\Report;
use App\Domain\Report\Models\ReportVersion;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function create(array $data, User $creator): Report
    {
        return DB::transaction(function () use ($data, $creator) {
            $report = Report::create($this->prepareData($data));
            // AuditLog::record('report.created', $report, $creator);
            return $report;
        });
    }

    public function update(Report $report, array $data, User $editor): Report
    {
        return DB::transaction(function () use ($report, $data, $editor) {
            $this->saveVersion($report, $editor);
            $report->update($this->prepareData($data));
            // AuditLog::record('report.updated', $report, $editor);
            return $report->fresh();
        });
    }

    public function publish(Report $report, User $publisher): void
    {
        if ($report->status === 'published') {
            throw new \DomainException('Relatório já está publicado.');
        }

        DB::transaction(function () use ($report, $publisher) {
            $this->saveVersion($report, $publisher);
            $report->update([
                'status'       => 'published',
                'published_at' => now(),
                'published_by' => $publisher->id,
            ]);
            // AuditLog::record('report.published', $report, $publisher);
        });
    }

    private function prepareData(array $data): array
    {
        // Garante que o mês de referência é uma data válida (primeiro dia do mês)
        if (!empty($data['reference_month']) && strlen($data['reference_month']) === 7) {
            $data['reference_month'] = $data['reference_month'] . '-01';
        }

        // Garante que valores financeiros não sejam nulos para o banco de dados
        $data['investment'] = $data['investment'] ?? 0;
        $data['revenue']    = $data['revenue']    ?? 0;

        // Nota: o campo 'roi' é GENERATED COLUMN no MySQL — calculado automaticamente
        // pelo banco como ((revenue - investment) / NULLIF(investment, 0)) * 100
        // Não deve ser inserido/atualizado explicitamente.
        unset($data['roi']);

        // Limpa top_contents vazios
        if (!empty($data['top_contents'])) {
            $data['top_contents'] = array_values(array_filter(
                $data['top_contents'],
                fn($c) => !empty($c['url'])
            ));
        }

        // Limpa locations vazias
        if (!empty($data['audience_locations'])) {
            $data['audience_locations'] = array_values(array_filter(
                $data['audience_locations'],
                fn($l) => !empty($l['city'])
            ));
        }

        return $data;
    }

    private function saveVersion(Report $report, User $user): void
    {
        ReportVersion::create([
            'report_id'     => $report->id,
            'version'       => $report->current_version,
            'data_snapshot' => $report->toArray(),
            'changed_by'    => $user->id,
        ]);
        $report->increment('current_version');
    }
}