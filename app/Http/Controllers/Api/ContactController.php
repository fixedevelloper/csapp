<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Devis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        Contact::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'],
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Exemple : envoi email
        Mail::raw($validated['message'], function ($mail) use ($validated) {
            $mail->to('info@cscreativ.com')
                ->subject($validated['subject'])
                ->replyTo($validated['email'], $validated['name']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès'
        ]);
    }
    public function storeDevis(Request $request)
    {
        // Validation des champs + honeypot anti-bot
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:20',
            'company'      => 'nullable|string|max:255',
            'project_type' => 'required|string|max:255',
            'budget'       => 'nullable|string|max:100',
            'description'  => 'required|string',
            'website'      => 'nullable|size:0', // honeypot
        ]);

        // Création du devis
        $devis = Devis::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone'        => $validated['phone'],
            'company'      => $validated['company'] ?? null,
            'project_type' => $validated['project_type'],
            'budget'       => $validated['budget'] ?? null,
            'description'  => $validated['description'],
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        // Optionnel : Notification admin par email
        // Mail::to('admin@creativsolutions.cm')->send(new NewDevisNotification($devis));

        return response()->json([
            'message' => 'Votre demande de devis a été envoyée avec succès.',
            'devis_id' => $devis->id,
        ]);
    }
    public function storeComment(Request $request)
    {
        $validated = $request->validate([
            'post_id' => ['required', 'exists:posts,id'],
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);
        $message = strip_tags($validated['message']);
        $comment = Comment::create([
            'post_id' => $validated['post_id'],
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'comment' => $message,
            'approved' => false, // modération
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire envoyé avec succès',
            'data' => $comment,
        ], 201);
    }
}

