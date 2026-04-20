<?php

namespace App\Domain\Media\Services;

use App\Domain\Media\Models\MediaLink;
use App\Domain\Client\Models\Client;
use Carbon\Carbon;

class MediaService
{
    public function create(array $data): MediaLink
    {
        $client = Client::where('uuid', $data['client_id'])->firstOrFail();

        return MediaLink::create([
            'client_id'   => $client->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'url'         => $data['url'],
            'type'        => $data['type'] ?? 'other',
            'month'       => (int) $data['month'],
            'year'        => (int) $data['year'],
            'is_public'   => true, // $data['is_public'] ?? false,
            'sort_order'  => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(MediaLink $media, array $data): MediaLink
    {
        $client = Client::where('uuid', $data['client_id'])->firstOrFail();

        $media->update([
            'client_id'   => $client->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'url'         => $data['url'],
            'type'        => $data['type'] ?? 'other',
            'month'       => (int) $data['month'],
            'year'        => (int) $data['year'],
            'is_public'   => $data['is_public'] ?? false,
        ]);

        return $media->fresh();
    }

    public function delete(MediaLink $media): void
    {
        $media->delete(); // SoftDelete
    }
}
