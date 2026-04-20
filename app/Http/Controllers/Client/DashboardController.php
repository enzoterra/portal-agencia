<?php

namespace App\Http\Controllers\Client;

use App\Domain\Financial\Models\Payment;
use App\Domain\Report\Models\Report;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $client      = auth()->user()->client;
        $showRoi     = $client->show_roi;

        $latestReport = $showRoi
            ? Report::published()->latest('reference_month')->first()
            : null;

        $nextPayment = Payment::where('status', 'pending')
            ->orWhere('status', 'overdue')
            ->orderBy('due_date')
            ->first();

        $roiHistory = $showRoi
            ? Report::published()
                ->orderBy('reference_month')
                ->limit(6)
                ->get(['reference_month', 'roi', 'investment', 'revenue'])
            : collect();

        $recentPayments = Payment::with('invoice')
            ->latest('due_date')
            ->limit(3)
            ->get();

        $recentReports = Report::published()
            ->latest('reference_month')
            ->limit(3)
            ->get();

        $totalReports    = Report::published()->count();
        $pendingPayments = Payment::where('status', 'pending')
            ->orWhere('status', 'overdue')
            ->count();

        return view('client.dashboard.index', compact(
            'client', 'showRoi', 'latestReport', 'nextPayment', 'roiHistory',
            'recentPayments', 'recentReports', 'totalReports', 'pendingPayments'
        ));
    }
}