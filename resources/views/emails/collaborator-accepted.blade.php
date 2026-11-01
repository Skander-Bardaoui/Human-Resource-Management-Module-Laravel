<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<title>Votre embauche est confirmée</title>
</head>
<body style="margin:0; padding:0; background-color:#F4F7FB; font-family:Arial, Helvetica, sans-serif; -webkit-font-smoothing:antialiased;">

<span style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">Votre embauche chez Carthage Tech Solutions est confirmée. Voici vos identifiants et les prochaines étapes de votre intégration.</span>

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
                    <td align="right" valign="middle" style="color:#A9C7E8; font-size:11px; letter-spacing:1.2px; text-transform:uppercase;">Carthage Tech Solutions</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Hero -->
    <tr>
        <td style="padding:40px 32px 8px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="background-color:#E8F4EC; border-radius:999px; padding:7px 14px;">
                        <span style="color:#1B5E3A; font-size:11px; font-weight:bold; letter-spacing:1.2px; text-transform:uppercase;">&#10003;&nbsp; Embauche confirmée</span>
                    </td>
                </tr>
            </table>
            <h1 style="margin:22px 0 0; color:#131B2E; font-size:27px; line-height:1.25; font-weight:bold;">Félicitations, {{ $employee->name }}&nbsp;!</h1>
            <p style="margin:14px 0 0; color:#464555; font-size:15px; line-height:1.7;">
                Nous avons le plaisir de vous annoncer que votre candidature au poste de
                <strong style="color:#131B2E;">{{ $employee->position }}</strong> a été retenue.
                Bienvenue dans l'équipe&nbsp;!
            </p>
        </td>
    </tr>

    <!-- Details -->
    <tr>
        <td style="padding:24px 32px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #DCE7F5; border-radius:12px; overflow:hidden;">
                <tr>
                    <td width="42%" style="padding:14px 18px; background-color:#F7FAFD; border-bottom:1px solid #DCE7F5; color:#77768A; font-size:12px; letter-spacing:0.6px; text-transform:uppercase;">Poste</td>
                    <td style="padding:14px 18px; border-bottom:1px solid #DCE7F5; color:#131B2E; font-size:14px; font-weight:bold;">{{ $employee->position }}</td>
                </tr>
                @if ($employee->contract_type)
                <tr>
                    <td width="42%" style="padding:14px 18px; background-color:#F7FAFD; border-bottom:1px solid #DCE7F5; color:#77768A; font-size:12px; letter-spacing:0.6px; text-transform:uppercase;">Type de contrat</td>
                    <td style="padding:14px 18px; border-bottom:1px solid #DCE7F5; color:#131B2E; font-size:14px; font-weight:bold;">{{ $employee->contract_type }}</td>
                </tr>
                @endif
                @if ($employee->hire_date)
                <tr>
                    <td width="42%" style="padding:14px 18px; background-color:#F7FAFD; color:#77768A; font-size:12px; letter-spacing:0.6px; text-transform:uppercase;">Date d'intégration</td>
                    <td style="padding:14px 18px; color:#131B2E; font-size:14px; font-weight:bold;">{{ $employee->hire_date->format('d/m/Y') }}</td>
                </tr>
                @endif
            </table>
        </td>
    </tr>

    <!-- Access -->
    <tr>
        <td style="padding:24px 32px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #DCE7F5; border-radius:12px; overflow:hidden;">
                <tr>
                    <td colspan="2" style="padding:14px 18px; background-color:#EDF3FB; border-bottom:1px solid #DCE7F5; color:#0F3D6E; font-size:13px; font-weight:bold; letter-spacing:0.4px;">Votre accès à la plateforme</td>
                </tr>
                <tr>
                    <td width="42%" style="padding:14px 18px; background-color:#F7FAFD; border-bottom:1px solid #DCE7F5; color:#77768A; font-size:12px; letter-spacing:0.6px; text-transform:uppercase;">Adresse de votre espace</td>
                    <td style="padding:14px 18px; border-bottom:1px solid #DCE7F5; font-size:14px;">
                        <a href="{{ route('login') }}" style="color:#0F3D6E; text-decoration:none; font-weight:bold;">{{ route('login') }}</a>
                    </td>
                </tr>
                <tr>
                    <td width="42%" style="padding:14px 18px; background-color:#F7FAFD; border-bottom:1px solid #DCE7F5; color:#77768A; font-size:12px; letter-spacing:0.6px; text-transform:uppercase;">Email / identifiant</td>
                    <td style="padding:14px 18px; border-bottom:1px solid #DCE7F5; color:#131B2E; font-size:14px; font-weight:bold;">{{ $employee->email }}</td>
                </tr>
                <tr>
                    <td width="42%" style="padding:14px 18px; background-color:#F7FAFD; color:#77768A; font-size:12px; letter-spacing:0.6px; text-transform:uppercase;">Mot de passe</td>
                    <td style="padding:14px 18px; color:#131B2E; font-size:14px; font-weight:bold; font-family:Consolas, Monaco, monospace; letter-spacing:0.5px;">{{ $password }}</td>
                </tr>
            </table>
            <p style="margin:14px 0 0; color:#77768A; font-size:13px; line-height:1.6;">
                Connectez-vous avec ces identifiants pour accéder à votre espace collaborateur (congés, fiches de paie, pointage et documents).
                Nous vous recommandons de modifier ce mot de passe après votre première connexion.
            </p>
        </td>
    </tr>

    <!-- Next steps -->
    <tr>
        <td style="padding:32px 32px 0;">
            <h2 style="margin:0 0 6px; color:#131B2E; font-size:16px; font-weight:bold;">Vos prochaines étapes</h2>
            <p style="margin:0 0 18px; color:#77768A; font-size:13px; line-height:1.6;">Préparez ces éléments avant votre premier jour.</p>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="34" valign="top" style="padding:0 0 16px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                            <tr><td width="26" height="26" align="center" valign="middle" style="width:26px; height:26px; background-color:#D6E6F7; border-radius:999px; color:#0F3D6E; font-size:12px; font-weight:bold;">1</td></tr>
                        </table>
                    </td>
                    <td valign="top" style="padding:2px 0 16px; color:#464555; font-size:14px; line-height:1.6;">Munissez-vous de votre <strong style="color:#131B2E;">CIN</strong> et de votre numéro <strong style="color:#131B2E;">CNSS</strong>.</td>
                </tr>
                <tr>
                    <td width="34" valign="top" style="padding:0 0 16px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                            <tr><td width="26" height="26" align="center" valign="middle" style="width:26px; height:26px; background-color:#D6E6F7; border-radius:999px; color:#0F3D6E; font-size:12px; font-weight:bold;">2</td></tr>
                        </table>
                    </td>
                    <td valign="top" style="padding:2px 0 16px; color:#464555; font-size:14px; line-height:1.6;">Rassemblez vos <strong style="color:#131B2E;">diplômes</strong> et <strong style="color:#131B2E;">attestations</strong> professionnelles.</td>
                </tr>
                <tr>
                    <td width="34" valign="top">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                            <tr><td width="26" height="26" align="center" valign="middle" style="width:26px; height:26px; background-color:#D6E6F7; border-radius:999px; color:#0F3D6E; font-size:12px; font-weight:bold;">3</td></tr>
                        </table>
                    </td>
                    <td valign="top" style="padding:2px 0 0; color:#464555; font-size:14px; line-height:1.6;">Notre équipe RH vous contactera pour finaliser votre <strong style="color:#131B2E;">dossier d'intégration</strong>.</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- CTA -->
    <tr>
        <td style="padding:30px 32px 0;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" style="background-color:#0F3D6E; border-radius:10px;">
                        <a href="{{ route('login') }}" style="display:inline-block; padding:14px 26px; color:#FFFFFF; font-size:14px; font-weight:bold; text-decoration:none; letter-spacing:0.2px;">Se connecter à mon espace</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Sign-off -->
    <tr>
        <td style="padding:30px 32px 36px;">
            <p style="margin:0; color:#464555; font-size:14px; line-height:1.7;">
                À très bientôt,<br>
                <strong style="color:#131B2E;">Direction des Ressources Humaines</strong><br>
                Carthage Tech Solutions
            </p>
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="background-color:#F5F8FC; padding:22px 32px; border-top:1px solid #DCE7F5;">
            <p style="margin:0; color:#77768A; font-size:12px; line-height:1.7;">
                E-mail envoyé automatiquement par <strong style="color:#464555;">HRM Pro</strong> pour le compte de Carthage Tech Solutions.<br>
                Une question&nbsp;? Répondez à <a href="mailto:{{ config('mail.from.address') }}" style="color:#0F3D6E; text-decoration:none;">{{ config('mail.from.address') }}</a>.
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
