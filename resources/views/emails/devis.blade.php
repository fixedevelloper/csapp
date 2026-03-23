<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande de devis</title>
</head>
<body style="margin:0; padding:0; background:#f4f6f9; font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9; padding:20px 0;">
    <tr>
        <td align="center">

            <!-- CONTAINER -->
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.05);">

                <!-- HEADER -->
                <tr>
                    <td style="background:linear-gradient(135deg, #4f46e5, #7c3aed); padding:20px; text-align:center; color:#fff;">
                        <h2 style="margin:0;">🚀 Nouvelle demande de devis</h2>
                        <p style="margin:5px 0 0; font-size:14px;">Une nouvelle demande vient d’être soumise</p>
                    </td>
                </tr>

                <!-- CONTENT -->
                <tr>
                    <td style="padding:30px;">

                        <table width="100%" cellpadding="8" cellspacing="0" style="font-size:14px; color:#333;">
                            <tr>
                                <td><strong>👤 Nom :</strong></td>
                                <td>{{ $devis->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>📧 Email :</strong></td>
                                <td>{{ $devis->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>📞 Téléphone :</strong></td>
                                <td>{{ $devis->phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>🏢 Entreprise :</strong></td>
                                <td>{{ $devis->company ?? 'Non renseigné' }}</td>
                            </tr>
                            <tr>
                                <td><strong>💼 Projet :</strong></td>
                                <td>{{ $devis->project_type }}</td>
                            </tr>
                            <tr>
                                <td><strong>💰 Budget :</strong></td>
                                <td>{{ $devis->budget ?? 'Non précisé' }}</td>
                            </tr>
                        </table>

                        <!-- DESCRIPTION -->
                        <div style="margin-top:20px; padding:15px; background:#f9fafb; border-radius:8px;">
                            <strong>📝 Description :</strong>
                            <p style="margin-top:10px; line-height:1.6;">
                                {{ $devis->description }}
                            </p>
                        </div>

                        <!-- BUTTON -->
                        <div style="text-align:center; margin-top:30px;">
                            <a href="mailto:{{ $devis->email }}"
                               style="display:inline-block; padding:12px 20px; background:#4f46e5; color:#fff; text-decoration:none; border-radius:6px; font-weight:bold;">
                                Répondre au client
                            </a>
                        </div>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#f4f6f9; text-align:center; padding:15px; font-size:12px; color:#888;">
                        © {{ date('Y') }} creativ solutions - Tous droits réservés
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
