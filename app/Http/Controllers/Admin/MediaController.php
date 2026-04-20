<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Media\Models\MediaLink;
use App\Domain\Media\Services\MediaService;
use App\Domain\Client\Models\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaLinkRequest;
use App\Http\Requests\Admin\UpdateMediaLinkRequest;
use App\Support\Traits\HasAuditLog;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    use HasAuditLog;

    public function __construct(
        private readonly MediaService $mediaService
    ) {
    }

    public function index(Request $request)
    {
        $clients = Client::orderBy('company_name')->get(['id', 'uuid', 'company_name', 'trade_name'])
            ->map(function ($c) {
                // Compatibilidade com o $client->name da view
                $c->name = $c->trade_name ?? $c->company_name;
                return $c;
            });

        $mediaLinks = MediaLink::with('client')
            ->when(
                $request->search,
                fn($q, $v) =>
                $q->where('title', 'like', "%{$v}%")
                    ->orWhere('description', 'like', "%{$v}%")
            )
            ->when(
                $request->client_id,
                fn($q, $uuid) =>
                $q->whereHas('client', fn($q) => $q->where('uuid', $uuid))
            )
            ->when(
                $request->month,
                fn($q, $m) =>
                $q->where('month', $m)
            )
            ->when(
                $request->year,
                fn($q, $y) =>
                $q->where('year', $y)
            )
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('sort_order')
            ->paginate(50)
            ->withQueryString();

        // Calcular stats
        $stats = [
            'total' => MediaLink::count(),
            'clients_with_media' => MediaLink::distinct('client_id')->count(),
            'this_month' => MediaLink::where('month', now()->month)
                ->where('year', now()->year)->count(),
            'today' => MediaLink::whereDate('created_at', today())->count(),
        ];

        // Agrupar mediaByClient para a view
        $grouped = $mediaLinks->getCollection()->groupBy('client_id')->map(function ($links) {
            $client = $links->first()->client;
            // Garantir a prop 'name'
            $client->name = $client->trade_name ?? $client->company_name;

            return [
                'client' => $client,
                'total' => $links->count(),
                'links' => $links,
            ];
        });

        // Substituir a coleção do paginador com a versão agrupada
        $mediaByClient = $mediaLinks->setCollection($grouped->values());

        return view('admin.media.index', compact('mediaByClient', 'clients', 'stats'));
    }

    public function store(StoreMediaLinkRequest $request)
    {
        $media = $this->mediaService->create($request->validated());
        $this->recordActivity('Created media link', $media, null, $media->toArray());

        return back()->with('success', 'Link de mídia adicionado com sucesso.');
    }

    public function update(UpdateMediaLinkRequest $request, MediaLink $media)
    {
        $oldValues = $media->toArray();
        $updated = $this->mediaService->update($media, $request->validated());
        $this->recordActivity('Updated media link', $updated, $oldValues, $updated->toArray());

        return back()->with('success', 'Link atualizado com sucesso.');
    }

    public function destroy(MediaLink $media)
    {
        $this->recordActivity('Deleted media link', $media, $media->toArray());
        $this->mediaService->delete($media);

        return back()->with('success', 'Link removido com sucesso.');
    }
}
