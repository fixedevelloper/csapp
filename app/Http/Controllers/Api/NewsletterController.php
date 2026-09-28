<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email'   => 'required|email|max:255',
            'website' => 'nullable|size:0', // honeypot
        ]);

        // firstOrCreate : une réinscription ne crée pas de doublon.
        // Même réponse que l'adresse soit nouvelle ou non, pour ne pas révéler qui est inscrit.
        NewsletterSubscriber::firstOrCreate(
            ['email' => Str::lower($validated['email'])],
            [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json([
            'message' => 'Merci ! Votre inscription à la newsletter est enregistrée.',
        ]);
    }
}
