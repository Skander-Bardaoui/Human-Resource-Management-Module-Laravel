Bonjour {{ $candidate->name }},

Nous avons le plaisir de vous annoncer que votre candidature au poste de {{ $candidate->position }} a été retenue et que votre embauche est confirmée. Bienvenue dans l'équipe !

DÉTAILS
Poste : {{ $candidate->position }}
@if ($candidate->contract_type_target)
Type de contrat : {{ $candidate->contract_type_target }}
@endif
@if ($candidate->hired_at)
Date d'intégration : {{ $candidate->hired_at->format('d/m/Y') }}
@endif

VOS PROCHAINES ÉTAPES
1. Munissez-vous de votre CIN et de votre numéro CNSS.
2. Rassemblez vos diplômes et attestations professionnelles.
3. Notre équipe RH vous contactera pour finaliser votre dossier d'intégration.

Une question ? Écrivez-nous à {{ config('mail.from.address') }}.

À très bientôt,
Direction des Ressources Humaines
Carthage Tech Solutions

--
E-mail envoyé automatiquement par HRM Pro pour le compte de Carthage Tech Solutions.
