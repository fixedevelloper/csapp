<?php


namespace App\Http\Controllers;


use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{

    public function index()
    {
        return view('newsletter.index');
    }

    /**
     * Export CSV des inscrits, importable dans Brevo, Mailchimp...
     */
    public function export()
    {
        $filename = 'newsletter-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'date_inscription']);

            NewsletterSubscriber::query()->orderBy('created_at')->chunk(500, function ($subscribers) use ($out) {
                foreach ($subscribers as $subscriber) {
                    fputcsv($out, [$subscriber->email, $subscriber->created_at->format('Y-m-d H:i:s')]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
