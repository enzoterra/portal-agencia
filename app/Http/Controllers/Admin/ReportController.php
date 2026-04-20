<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Client\Models\Client;
use App\Domain\Report\Models\Report;
use App\Domain\Report\Services\ReportService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReportRequest;
use App\Http\Requests\Admin\UpdateReportRequest;
use App\Support\Traits\HasAuditLog;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReportController extends Controller
{
    use HasAuditLog;

    public function __construct(private readonly ReportService $service) {}

    public function index(): View
    {
        $reports = Report::with('client')
            ->withoutGlobalScope('client')
            ->latest('reference_month')
            ->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    public function create(): View
    {
        $clients = Client::active()->orderBy('company_name')->get();
        return view('admin.reports.create', compact('clients'));
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->input('action') === 'publish') {
            $data['status'] = 'published';
            $data['published_at'] = now();
            $data['published_by'] = auth()->id();
        } else {
            $data['status'] = 'draft';
        }

        try {
            $report = $this->service->create($data, auth()->user());
            $this->recordActivity('Created report', $report, null, $report->toArray());

            return redirect()
                ->route('admin.relatorios.index')
                ->with('success', 'Relatório criado com sucesso.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Integrity constraint violation (unique)
                return back()
                    ->withInput()
                    ->with('error', 'Já existe um relatório para este cliente neste mês.');
            }
            Log::error('Erro de banco ao criar relatório: ' . $e->getMessage(), ['data' => $data, 'exception' => $e]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erro inesperado ao criar relatório: ' . $e->getMessage(), ['data' => $data, 'exception' => $e]);
            return back()
                ->withInput()
                ->with('error', 'Ocorreu um erro inesperado ao salvar o relatório. Verifique os logs.');
        }
    }

    public function show(Report $report): RedirectResponse
    {
        // Admin does not have a show view currently, redirect to edit
        return redirect()->route('admin.relatorios.edit', $report);
    }

    public function edit(Report $report): View
    {
        $clients = Client::active()->orderBy('company_name')->get();
        return view('admin.reports.edit', compact('report', 'clients'));
    }

    public function update(UpdateReportRequest $request, Report $report): RedirectResponse
    {
        $oldValues = $report->toArray();
        $data = $request->validated();

        if ($request->input('action') === 'publish' && $report->status !== 'published') {
            $data['status'] = 'published';
            $data['published_at'] = now();
            $data['published_by'] = auth()->id();
        }

        try {
            $updated = $this->service->update($report, $data, auth()->user());
            $this->recordActivity('Updated report', $updated, $oldValues, $updated->toArray());

            return redirect()
                ->route('admin.relatorios.index')
                ->with('success', 'Relatório atualizado com sucesso.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()
                    ->withInput()
                    ->with('error', 'Já existe um relatório para este cliente neste mês.');
            }
            Log::error('Erro de banco ao atualizar relatório: ' . $e->getMessage(), ['id' => $report->id, 'exception' => $e]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erro inesperado ao atualizar relatório: ' . $e->getMessage(), ['id' => $report->id, 'exception' => $e]);
            return back()
                ->withInput()
                ->with('error', 'Ocorreu um erro inesperado ao atualizar o relatório.');
        }
    }

    public function destroy(Report $report): RedirectResponse
    {
        $this->recordActivity('Deleted report', $report, $report->toArray());
        $report->forceDelete();

        return redirect()
            ->route('admin.relatorios.index')
            ->with('success', 'Relatório removido.');
    }

    public function publish(Report $report): RedirectResponse
    {
        $this->service->publish($report, auth()->user());
        $this->recordActivity('Published report', $report);

        return redirect()
            ->route('admin.relatorios.index')
            ->with('success', 'Relatório publicado com sucesso.');
    }

    public function archive(Report $report): RedirectResponse
    {
        $report->update(['status' => 'archived']);
        $this->recordActivity('Archived report', $report);

        return redirect()
            ->route('admin.relatorios.index')
            ->with('success', 'Relatório arquivado.');
    }
}