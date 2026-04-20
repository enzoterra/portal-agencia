<?php

namespace App\Http\Controllers\Client;

use App\Domain\Calendar\Models\CalendarEvent;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Mês visualizado (padrão: mês atual)
        $currentMonth = $request->month
            ? Carbon::parse($request->month . '-01')->startOfMonth()
            : now()->startOfMonth();

        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        $clientId = auth()->user()->client_id;

        // Busca eventos do mês:
        // - Eventos específicos deste cliente (client_id = $clientId)
        $monthEvents = CalendarEvent::where('status', '!=', 'cancelled')
            ->where('client_id', $clientId)
            ->whereYear('starts_at', $currentMonth->year)
            ->whereMonth('starts_at', $currentMonth->month)
            ->orderBy('starts_at')
            ->get();

        // Agrupa por data (string 'Y-m-d') para a grade
        $eventsByDay = $monthEvents->groupBy(fn($e) => $e->starts_at->toDateString());

        // Próximos 30 dias a partir de hoje (sidebar)
        $upcomingEvents = CalendarEvent::where('status', '!=', 'cancelled')
            ->where('client_id', $clientId)
            ->where('starts_at', '>=', now()->startOfDay())
            ->where('starts_at', '<=', now()->addDays(30))
            ->orderBy('starts_at')
            ->limit(10)
            ->get();

        return view('client.calendar.index', compact(
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'eventsByDay',
            'upcomingEvents'
        ));
    }
}