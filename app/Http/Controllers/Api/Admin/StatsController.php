<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Devis;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    // Compteurs pour le tableau de bord et les pastilles du menu
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'devis_new' => Devis::where('status', 'nouveau')->count(),
            'devis_total' => Devis::count(),
            'contacts_unread' => Contact::where('is_read', false)->count(),
            'contacts_total' => Contact::count(),
            'comments_pending' => Comment::where('approved', false)->count(),
            'newsletter_total' => NewsletterSubscriber::count(),
        ]);
    }
}
