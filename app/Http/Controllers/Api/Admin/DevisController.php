<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminDevisResource;
use App\Models\Devis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DevisController extends Controller
{
    public const STATUSES = ['nouveau', 'en_cours', 'envoye', 'accepte'];

    public function index(Request $request)
    {
        $request->validate([
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer'],
        ]);

        $perPage = min(max((int) $request->get('per_page', 15), 1), 100);

        $query = Devis::query()
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->q, function ($q, $term) {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], strip_tags($term)).'%';
                $q->where(fn ($i) => $i->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('company', 'like', $term)
                    ->orWhere('project_type', 'like', $term)
                    ->orWhere('description', 'like', $term));
            })
            ->latest();

        $counts = Devis::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return AdminDevisResource::collection($query->paginate($perPage))->additional([
            'counts' => collect(self::STATUSES)->mapWithKeys(fn ($s) => [$s => (int) ($counts[$s] ?? 0)])
                ->put('all', (int) $counts->sum()),
        ]);
    }

    public function show(Devis $devis): AdminDevisResource
    {
        return new AdminDevisResource($devis);
    }

    public function update(Request $request, Devis $devis): AdminDevisResource
    {
        $data = $request->validate(['status' => ['required', Rule::in(self::STATUSES)]]);
        $devis->update($data);

        return new AdminDevisResource($devis);
    }

    public function destroy(Devis $devis): JsonResponse
    {
        $devis->delete();

        return response()->json(['message' => 'Devis supprimé.']);
    }
}
