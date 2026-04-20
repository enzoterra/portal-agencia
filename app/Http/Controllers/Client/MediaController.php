<?php

namespace App\Http\Controllers\Client;

use App\Domain\Media\Models\MediaLink;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        // BelongsToClient GlobalScope já filtra por client_id automaticamente.
        // Só exibe links marcados como públicos (is_public = true).
        $query = MediaLink::where('is_public', true)
            ->when($request->year, fn($q, $year) =>
                $q->where('year', $year)
            )
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('sort_order');

        $all = $query->get();

        // Anos disponíveis para as tabs de filtro
        $years = MediaLink::where('is_public', true)
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Agrupa por mês/ano para exibição
        $mediaByMonth = $all->groupBy(fn($m) => "{$m->year}-" . str_pad($m->month, 2, '0', STR_PAD_LEFT));

        return view('client.media.index', compact('mediaByMonth', 'years'));
    }
}