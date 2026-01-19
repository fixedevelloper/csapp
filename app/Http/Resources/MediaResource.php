<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->getUrl(), // Spatie Media Library URL
            'thumbnail' => $this->getUrl('thumb'), // conversion thumbnail si définie
            'type' => $this->mime_type,
            'collection' => $this->collection_name,
            'size' => $this->size,
        ];
    }
}
