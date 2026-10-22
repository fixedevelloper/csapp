<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactSubmitted;
use App\Mail\DevisSubmitted;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Devis;
use App\Support\DevisSchema;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    // Envoi immédiat (sans file d'attente, qui exige un worker en production) ; un échec SMTP est journalisé
    // sans faire perdre la demande, déjà enregistrée en base et visible dans l'admin
    private function notify(string $to, Mailable $mail): void
    {
        try {
            Mail::to($to)->send($mail);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'website' => 'nullable|size:0', // honeypot
        ]);
        $contact = Contact::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'],
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->notify(config('mail.recipients.contact'), new ContactSubmitted($contact));

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès'
        ]);
    }
    public function storeDevis(Request $request)
    {
        // Validation des champs + honeypot anti-bot ; les questions propres au service sont validées selon project_type
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:20',
            'company'      => 'nullable|string|max:255',
            'project_type' => 'required|string|max:255',
            'budget'       => 'nullable|string|max:100',
            'deadline'     => ['nullable', 'string', Rule::in(DevisSchema::DEADLINES)],
            'source'       => ['nullable', 'string', 'max:150', 'regex:/^\/[\w\-\/]*$/'],
            // Les services guidés (questions fermées) n'exigent pas de description libre
            'description'  => [Rule::requiredIf(!in_array($request->input('project_type'), DevisSchema::guidedTypes(), true)), 'nullable', 'string', 'max:5000'],
            'website'      => 'nullable|size:0', // honeypot
        ] + DevisSchema::rules($request->input('project_type')));

        // Création du devis
        $devis = Devis::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone'        => $validated['phone'],
            'company'      => $validated['company'] ?? null,
            'project_type' => $validated['project_type'],
            'budget'       => $validated['budget'] ?? null,
            'deadline'     => $validated['deadline'] ?? null,
            'source'       => $validated['source'] ?? null,
            'details'      => $validated['details'] ?? null,
            'description'  => $validated['description'] ?? null,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        $this->notify(config('mail.recipients.devis'), new DevisSubmitted($devis));
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

