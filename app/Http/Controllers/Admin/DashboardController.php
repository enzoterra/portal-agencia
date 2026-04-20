<?php
// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Domain\Calendar\Models\CalendarEvent;
use App\Domain\Client\Models\Client;
use App\Domain\Financial\Models\Payment;
use App\Domain\Report\Models\Report;
use App\Domain\Media\Models\MediaLink;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Totais gerais ─────────────────────────────────────
        $totalClients      = Client::count();
        $activeClients     = Client::active()->count();
        $totalReports      = Report::withoutGlobalScope('client')->count();
        $publishedReports  = Report::withoutGlobalScope('client')->where('status', 'published')->count();

        // ── Financeiro ────────────────────────────────────────
        $financial = Payment::withoutGlobalScope('client')
            ->selectRaw("
                SUM(CASE WHEN status = 'paid'    THEN amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as total_pending,
                SUM(CASE WHEN status = 'overdue' THEN amount ELSE 0 END) as total_overdue,
                COUNT(CASE WHEN status = 'overdue' THEN 1 END)           as overdue_count,
                COUNT(CASE WHEN status = 'pending' THEN 1 END)           as pending_count
            ")->first();

        // ── Receita total (soma de revenue dos relatórios publicados) ──
        $totalRevenue = Report::withoutGlobalScope('client')
            ->where('status', 'published')
            ->sum('revenue');

        // ── Clientes com pagamento vencido ────────────────────
        $overdueClients = Client::whereHas('payments', fn($q) =>
            $q->where('status', 'overdue')
        )->with(['payments' => fn($q) =>
            $q->where('status', 'overdue')->orderBy('due_date')
        ])->limit(5)->get();

        // ── Últimos relatórios publicados ─────────────────────
        $recentReports = Report::withoutGlobalScope('client')
            ->with('client')
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(5)
            ->get();

        // ── Relatórios em rascunho (precisam de atenção) ──────
        $draftReports = Report::withoutGlobalScope('client')
            ->with('client')
            ->where('status', 'draft')
            ->latest('updated_at')
            ->limit(5)
            ->get();

        // ── Clientes ativos com ROI do último relatório ───────
        $clientsWithRoi = Client::active()
            ->with(['reports' => fn($q) =>
                $q->where('status', 'published')
                  ->latest('reference_month')
                  ->limit(1)
            ])
            ->limit(6)
            ->get();

        // ── Receita por mês (últimos 6 meses) ─────────────────
        $revenueByMonth = Report::withoutGlobalScope('client')
            ->where('status', 'published')
            ->selectRaw("TO_CHAR(reference_month, 'YYYY-MM') as month, SUM(revenue) as revenue, SUM(investment) as investment")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get();

        // ── Calendário (próximos eventos para o card do dashboard) ──
        $calendarUpcoming = CalendarEvent::where('status', '!=', 'cancelled')
            ->where('starts_at', '>=', now()->startOfDay())
            ->where('starts_at', '<=', now()->addDays(7))
            ->orderBy('starts_at')
            ->with('client')
            ->limit(8)
            ->get();

        $calendarToday = $calendarUpcoming->filter(
            fn($e) => $e->starts_at->isToday()
        );

        $calendarThisMonth = CalendarEvent::where('status', '!=', 'cancelled')
            ->whereYear('starts_at', now()->year)
            ->whereMonth('starts_at', now()->month)
            ->count();

        return view('admin.dashboard', compact(
            'totalClients', 'activeClients',
            'totalReports', 'publishedReports',
            'financial', 'totalRevenue',
            'overdueClients', 'recentReports', 'draftReports',
            'clientsWithRoi', 'revenueByMonth',
            'calendarUpcoming', 'calendarToday', 'calendarThisMonth'
        ));
    }
}