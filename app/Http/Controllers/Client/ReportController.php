<?php

namespace App\Http\Controllers\Client;

use App\Domain\Report\Models\Report;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('reference_month');

        $query = Report::where('status', 'published')
            ->orderByDesc('reference_month');

        if ($month) {
            $query->where('reference_month', Carbon::parse($month . '-01')->toDateString());
        }

        $reports = $query->paginate(12)->withQueryString();

        // Redireciona direto se o usuário *filtrou* e encontrou apenas 1
        if ($month && $reports->total() === 1) {
            return redirect()->route('cliente.relatorios.show', $reports->first()->uuid);
        }

        return view('client.reports.index', compact('reports', 'month'));
    }

    public function show(Report $report)
    {
        // Garante que o cliente só vê relatórios publicados
        // (o BelongsToClient GlobalScope já filtra por client_id)
        abort_if($report->status !== 'published', 404);

        // Relatório anterior e próximo para navegação
        $prev = Report::where('status', 'published')
            ->where('reference_month', '<', $report->reference_month)
            ->orderByDesc('reference_month')
            ->first(['uuid', 'reference_month']);

        $next = Report::where('status', 'published')
            ->where('reference_month', '>', $report->reference_month)
            ->orderBy('reference_month')
            ->first(['uuid', 'reference_month']);

        $availableReports = Report::where('status', 'published')
            ->orderByDesc('reference_month')
            ->get(['uuid', 'reference_month']);

        return view('client.reports.show', compact('report', 'prev', 'next', 'availableReports'));
    }
}