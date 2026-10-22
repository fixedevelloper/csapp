<?php

namespace App\Models;

use App\Support\DevisSchema;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $casts = ['details' => 'array'];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'project_type',
        'budget',
        'deadline',
        'source',
        'details',
        'description',
        'status',
        'ip_address',
        'user_agent',
    ];

    /** Réponses aux questions du service, sous forme [{label, value}]. */
    public function detailsList(): array
    {
        return DevisSchema::present($this->project_type, $this->details);
    }
}
