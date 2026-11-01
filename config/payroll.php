<?php

/*
|--------------------------------------------------------------------------
| Paramètres de paie
|--------------------------------------------------------------------------
|
| Les taux et barèmes ci-dessous sont des valeurs de travail à faire
| valider par la direction financière avant mise en production. Chaque
| montant est exprimé en millimes (1 TND = 1000 millimes) et chaque taux
| en points de base (918 = 9,18 %). Changez "version" à chaque révision
| du paramétrage pour garder une trace dans l'interface et les bulletins.
|
*/

return [
    'version' => '2026.1',
    'timezone' => 'Africa/Tunis',

    'company' => [
        'name' => 'Carthage Tech Solutions SARL',
        'address' => 'Tunis Lac II - MF 182934 A/M/000',
        'cnss_number' => '29481900-12',
    ],

    'cnss' => [
        'employee_rate_bp' => 918,
        'employer_rate_bp' => 1737,
    ],

    'css_rate_bp' => 100,

    'transport_allowance_millimes' => 100000,

    'irpp' => [
        'annual_deduction_per_part_millimes' => 5000000,
        'brackets' => [
            ['up_to_millimes' => 5000000, 'rate_bp' => 0],
            ['up_to_millimes' => 20000000, 'rate_bp' => 2600],
            ['up_to_millimes' => 30000000, 'rate_bp' => 2800],
            ['up_to_millimes' => 40000000, 'rate_bp' => 3200],
            ['up_to_millimes' => 50000000, 'rate_bp' => 3400],
            ['up_to_millimes' => 70000000, 'rate_bp' => 3600],
            ['up_to_millimes' => null, 'rate_bp' => 3800],
        ],
    ],

    'family' => [
        'spouse_half_parts' => 1,
        'child_half_parts' => 1,
        'max_child_half_parts' => 3,
    ],

    /*
    | Salaire de base utilisé à la première génération d'un bulletin,
    | lorsqu'aucun bulletin antérieur n'existe pour le collaborateur.
    */
    'salary_grid_millimes' => [
        'it' => 3400000,
        'finance' => 3200000,
        'produit' => 3100000,
        'commerce' => 2900000,
        'rh' => 2800000,
    ],
    'default_base_salary_millimes' => 2800000,

    'payment_deadline_day' => 28,
    'irpp_declaration_day' => 28,
];
