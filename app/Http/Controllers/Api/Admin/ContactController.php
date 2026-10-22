<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'unread' => ['nullable', 'boolean'],
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer'],
        ]);

        $perPage = min(max((int) $request->get('per_page', 15), 1), 100);

        $query = Contact::query()
            ->when($request->boolean('unread'), fn ($q) => $q->where('is_read', false))
            ->when($request->q, function ($q, $term) {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], strip_tags($term)).'%';
                $q->where(fn ($i) => $i->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('subject', 'like', $term)
                    ->orWhere('message', 'like', $term));
            })
            ->latest();

        return AdminContactResource::collection($query->paginate($perPage))->additional([
            'counts' => [
                'all' => Contact::count(),
                'unread' => Contact::where('is_read', false)->count(),
            ],
        ]);
    }

    public function show(Contact $contact): AdminContactResource
    {
        return new AdminContactResource($contact);
    }

    public function update(Request $request, Contact $contact): AdminContactResource
    {
        $data = $request->validate(['is_read' => ['required', 'boolean']]);
        $contact->update($data);

        return new AdminContactResource($contact);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json(['message' => 'Message supprimé.']);
    }
}
