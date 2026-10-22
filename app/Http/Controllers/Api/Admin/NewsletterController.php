<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate(['q' => ['nullable', 'string', 'max:100'], 'per_page' => ['nullable', 'integer']]);
        $perPage = min(max((int) $request->get('per_page', 20), 1), 100);

        $page = NewsletterSubscriber::query()
            ->when($request->q, function ($q, $term) {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], strip_tags($term)).'%';
                $q->where('email', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $page->getCollection()->map(fn ($s) => [
                'id' => $s->id,
                'email' => $s->email,
                'created_at' => $s->created_at?->toIso8601String(),
            ])->values(),
            'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()],
            'counts' => ['all' => NewsletterSubscriber::count()],
        ]);
    }

    // Désinscription sur demande (droit à l'effacement RGPD)
    public function destroy(NewsletterSubscriber $subscriber): JsonResponse
    {
        $subscriber->delete();

        return response()->json(['message' => 'Inscrit supprimé.']);
    }

    /**
     * Export CSV des inscrits, importable dans Brevo, Mailchimp...
     */
    public function export()
    {
        $filename = 'newsletter-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'date_inscription']);

            NewsletterSubscriber::query()->orderBy('created_at')->chunk(500, function ($subscribers) use ($out) {
                foreach ($subscribers as $s) {
                    // Neutralise l'injection de formules si le CSV est ouvert dans un tableur
                    $email = preg_match('/^[=+\-@\t\r]/', $s->email) ? "'".$s->email : $s->email;
                    fputcsv($out, [$email, $s->created_at->format('Y-m-d H:i:s')]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
