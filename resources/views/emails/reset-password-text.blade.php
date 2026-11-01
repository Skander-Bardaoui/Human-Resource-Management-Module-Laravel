Bonjour {{ $name }},

Nous avons reçu une demande de réinitialisation du mot de passe associé à {{ $email }}.

Pour choisir un nouveau mot de passe, ouvrez ce lien :
{{ $url }}

Ce lien est valable {{ $expire }} minutes et ne peut être utilisé qu'une seule fois.

Vous n'êtes pas à l'origine de cette demande ? Ignorez simplement cet e-mail : votre mot de passe actuel restera inchangé.

À très bientôt,
L'équipe HRM Pro
Carthage Tech Solutions

--
E-mail envoyé automatiquement par HRM Pro pour le compte de Carthage Tech Solutions.
Besoin d'aide ? Répondez à {{ config('mail.from.address') }}.
