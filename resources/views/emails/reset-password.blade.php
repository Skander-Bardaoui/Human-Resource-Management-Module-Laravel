<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<title>Réinitialisation de votre mot de passe</title>
</head>
<body style="margin:0; padding:0; background-color:#F4F7FB; font-family:Arial, Helvetica, sans-serif; -webkit-font-smoothing:antialiased;">

<span style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">Choisissez un nouveau mot de passe pour votre compte HRM Pro. Ce lien est valable {{ $expire }} minutes.</span>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F4F7FB; padding:40px 16px;">
<tr>
<td align="center">

<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; background-color:#FFFFFF; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(19,27,46,0.08);">

    <!-- Header -->
    <tr>
        <td style="background-color:#0F3D6E; padding:28px 32px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="left" valign="middle">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="44" height="44" align="center" valign="middle" style="width:44px; height:44px; background-color:#FFFFFF; border-radius:12px; color:#0F3D6E; font-size:16px; font-weight:bold; letter-spacing:0.5px;">HR</td>
                                <td style="padding-left:14px;">
                                    <div style="color:#FFFFFF; font-size:20px; font-weight:bold; letter-spacing:0.3px;">HRM Pro</div>
                                    <div style="color:#A9C7E8; font-size:11px; letter-spacing:1.6px; text-transform:uppercase; margin-top:3px;">Ressources Humaines</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td align="right" valign="middle" style="color:#A9C7E8; font-size:11px; letter-spacing:1.2px; text-transform:uppercase;">Sécurité du compte</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Hero -->
    <tr>
        <td style="padding:40px 32px 8px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="background-color:#E8F1FA; border-radius:999px; padding:7px 14px;">
                        <span style="color:#0F3D6E; font-size:11px; font-weight:bold; letter-spacing:1.2px; text-transform:uppercase;">&#128274;&nbsp; Réinitialisation demandée</span>
                    </td>
                </tr>
            </table>
            <h1 style="margin:22px 0 0; color:#131B2E; font-size:27px; line-height:1.25; font-weight:bold;">Réinitialisez votre mot de passe</h1>
            <p style="margin:14px 0 0; color:#464555; font-size:15px; line-height:1.7;">
                Bonjour <strong style="color:#131B2E;">{{ $name }}</strong>, nous avons reçu une demande de réinitialisation du mot de passe associé à
                <strong style="color:#131B2E;">{{ $email }}</strong>.
            </p>
            <p style="margin:10px 0 0; color:#464555; font-size:15px; line-height:1.7;">
                Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
            </p>
        </td>
    </tr>

    <!-- CTA -->
    <tr>
        <td style="padding:28px 32px 0;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" style="background-color:#0F3D6E; border-radius:10px;">
                        <a href="{{ $url }}" style="display:inline-block; padding:15px 28px; color:#FFFFFF; font-size:14px; font-weight:bold; text-decoration:none; letter-spacing:0.2px;">Choisir un nouveau mot de passe</a>
                    </td>
                </tr>
            </table>
            <p style="margin:16px 0 0; color:#77768A; font-size:12px; line-height:1.6;">
                Ce lien est valable <strong style="color:#464555;">{{ $expire }} minutes</strong> et ne peut être utilisé qu'une seule fois.
            </p>
        </td>
    </tr>

    <!-- Expiry / security -->
    <tr>
        <td style="padding:28px 32px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F7FAFD; border:1px solid #DCE7F5; border-radius:12px;">
                <tr>
                    <td style="padding:18px 20px;">
                        <p style="margin:0 0 6px; color:#0F3D6E; font-size:13px; font-weight:bold;">Vous n'êtes pas à l'origine de cette demande ?</p>
                        <p style="margin:0; color:#77768A; font-size:13px; line-height:1.6;">
                            Ignorez simplement cet e-mail : votre mot de passe actuel restera inchangé. Personne n'a accès à votre compte tant que le lien n'est pas utilisé.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Fallback URL -->
    <tr>
        <td style="padding:24px 32px 0;">
            <p style="margin:0 0 6px; color:#77768A; font-size:12px;">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :</p>
            <p style="margin:0; font-size:12px; line-height:1.6; word-break:break-all;">
                <a href="{{ $url }}" style="color:#0F3D6E; text-decoration:underline;">{{ $url }}</a>
            </p>
        </td>
    </tr>

    <!-- Sign-off -->
    <tr>
        <td style="padding:28px 32px 36px;">
            <p style="margin:0; color:#464555; font-size:14px; line-height:1.7;">
                À très bientôt,<br>
                <strong style="color:#131B2E;">L'équipe HRM Pro</strong><br>
                Carthage Tech Solutions
            </p>
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="background-color:#F5F8FC; padding:22px 32px; border-top:1px solid #DCE7F5;">
            <p style="margin:0; color:#77768A; font-size:12px; line-height:1.7;">
                E-mail envoyé automatiquement par <strong style="color:#464555;">HRM Pro</strong> pour le compte de Carthage Tech Solutions.<br>
                Besoin d'aide ? Répondez à <a href="mailto:{{ config('mail.from.address') }}" style="color:#0F3D6E; text-decoration:none;">{{ config('mail.from.address') }}</a>.
            </p>
        </td>
    </tr>

</table>

<p style="margin:18px 0 0; color:#9A98AC; font-size:11px; line-height:1.6;">&copy; {{ date('Y') }} HRM Pro &middot; Plateforme de gestion des ressources humaines</p>

</td>
</tr>
</table>

</body>
</html>
