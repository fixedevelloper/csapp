<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminDevisResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'project_type' => $this->project_type,
            'budget' => $this->budget,
            'deadline' => $this->deadline,
            'source' => $this->source,
            'details' => $this->detailsList(),
            'description' => $this->description,
            'status' => $this->status,
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
