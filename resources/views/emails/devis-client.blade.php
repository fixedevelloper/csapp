<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre demande de devis</title>
</head>
<body style="margin:0; padding:0; background:#f4f6f9; font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9; padding:20px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.05);">

                <tr>
                    <td style="background:#086A27; padding:24px; text-align:center; color:#fff;">
                        <h2 style="margin:0;">Demande bien reçue</h2>
                        <p style="margin:6px 0 0; font-size:14px;">Creativ Solutions</p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:30px; font-size:14px; color:#333; line-height:1.6;">
                        <p>Bonjour {{ $devis->name }},</p>
                        <p>Merci pour votre demande de devis. Nous l'avons bien reçue et nous revenons vers vous <strong>sous 24 h</strong> avec une première proposition.</p>

                        <div style="margin:20px 0; padding:15px; background:#f4f8f4; border-radius:8px;">
                            <strong>Récapitulatif de votre demande</strong>
                            <table width="100%" cellpadding="6" cellspacing="0" style="font-size:14px; color:#333; margin-top:8px;">
                                <tr><td style="width:45%;"><strong>Service</strong></td><td>{{ $devis->project_type }}</td></tr>
                                @if($devis->budget)<tr><td><strong>Budget estimatif</strong></td><td>{{ $devis->budget }}</td></tr>@endif
                                @if($devis->deadline)<tr><td><strong>Délai souhaité</strong></td><td>{{ $devis->deadline }}</td></tr>@endif
                                @foreach($devis->detailsList() as $d)
                                <tr><td style="vertical-align:top;"><strong>{{ $d['label'] }}</strong></td><td>{{ $d['value'] }}</td></tr>
                                @endforeach
                            </table>
                        </div>

                        <p>Une précision à ajouter ? Répondez simplement à cet e-mail, ou écrivez-nous sur WhatsApp au <a href="https://wa.me/237675066919" style="color:#086A27;">+237 675 066 919</a>.</p>
                        <p>À très bientôt,<br>L'équipe Creativ Solutions</p>
                    </td>
                </tr>

                <tr>
                    <td style="background:#f4f6f9; text-align:center; padding:15px; font-size:12px; color:#888;">
                        © {{ date('Y') }} Creativ Solutions · Vous recevez cet e-mail car une demande de devis a été faite avec cette adresse.
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
