<?php

namespace App\Support;

use App\Models\Employee;
use Carbon\Carbon;

class HrDocumentPdf
{
    public static function attestationTravail(Employee $employee, ?Carbon $issuedAt = null): string
    {
        $issuedAt = $issuedAt ?? AttendanceClock::now();
        $ops = [];
        $y = 800;

        $ops[] = self::text(50, $y, 17, 'F2', 'CARTHAGE TECH SOLUTIONS SARL');
        $ops[] = self::text(50, $y - 17, 9, 'F1', 'Tunis Lac II - MF 182934 A/M/000 - CNSS Employeur 29481900-12');
        $ops[] = self::text(50, $y - 40, 14, 'F2', 'ATTESTATION DE TRAVAIL');
        $ops[] = self::line(50, $y - 54, 545);

        $y = 690;
        $ops[] = self::text(50, $y, 9, 'F1', 'Tunis, le '.$issuedAt->locale('fr')->translatedFormat('j F Y'));
        $ops[] = self::text(50, $y - 24, 9, 'F2', 'Reference : '.$employee->matricule().'-ATT-'.$issuedAt->format('Ymd'));

        $y -= 70;
        $ops[] = self::text(50, $y, 10, 'F1', 'Nous, soussignes CARTHAGE TECH SOLUTIONS SARL, attestons que :');
        $y -= 28;
        $ops[] = self::text(50, $y, 12, 'F2', 'Mme / M. '.$employee->name);
        $y -= 22;
        $ops[] = self::text(50, $y, 10, 'F1', 'CIN : '.($employee->cin ?: '—').'    CNSS : '.($employee->cnss_number ?: '—'));
        $y -= 22;
        $ops[] = self::text(50, $y, 10, 'F1', 'Matricule : '.$employee->matricule().'    Departement : '.$employee->departmentLabel());

        $y -= 40;
        $hireDate = $employee->hire_date
            ? $employee->hire_date->locale('fr')->translatedFormat('j F Y')
            : 'la date de son recrutement';

        $ops[] = self::text(50, $y, 10, 'F1', 'est employe(e) au sein de notre societe depuis le '.$hireDate.'.');
        $y -= 22;
        $ops[] = self::text(50, $y, 10, 'F1', 'Poste occupe : '.($employee->position ?: '—').'    Contrat : '.($employee->contract_type ?: 'CDI').'.');
        $y -= 22;
        $ops[] = self::text(50, $y, 10, 'F1', 'Statut : '.$employee->statusLabel().'.');

        $y -= 50;
        $ops[] = self::text(50, $y, 10, 'F1', 'La presente attestation est delivree a l\'interesse(e) pour servir et valoir ce que de droit.');
        $y -= 22;
        $ops[] = self::text(50, $y, 10, 'F1', 'Elle ne comporte aucune mention de remuneration.');

        $y -= 90;
        $ops[] = self::text(50, $y, 9, 'F2', 'Pour CARTHAGE TECH SOLUTIONS SARL');
        $ops[] = self::text(50, $y - 18, 9, 'F1', 'Departement Ressources Humaines');
        $ops[] = self::text(50, $y - 70, 8, 'F2', 'SIGNE ELECTRONIQUEMENT');
        $ops[] = self::text(50, $y - 84, 8, 'F1', 'Horodatage : '.$issuedAt->format('d/m/Y H:i:s'));
        $ops[] = self::text(330, $y - 70, 8, 'F1', 'Verification : '.strtoupper(substr(hash('sha256', $employee->id.'|'.$issuedAt->format('YmdHi')), 0, 32)));

        $ops[] = self::line(50, 70, 545);
        $ops[] = self::text(50, 56, 8, 'F1', 'Document genere automatiquement par HRM Pro - Code du Travail Tunisien.');

        return self::build($ops);
    }

    private static function build(array $ops): string
    {
        $content = implode("\n", $ops);
        $length = strlen($content);

        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
            6 => '<< /Length '.$length." >>\nstream\n".$content."\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $index => $object) {
            $offsets[$index] = strlen($pdf);
            $pdf .= $index." 0 obj\n".$object."\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF";

        return $pdf;
    }

    private static function text(float $x, float $y, int $size, string $font, string $text): string
    {
        return 'BT /'.$font.' '.$size.' Tf 1 0 0 1 '.self::number($x).' '.self::number($y).' Tm ('.self::escape($text).') Tj ET';
    }

    private static function line(float $x, float $y, float $width): string
    {
        return '0.6 w 0.75 0.75 0.8 RG '.self::number($x).' '.self::number($y).' m '.self::number($x + $width).' '.self::number($y).' l S';
    }

    private static function number(float $number): string
    {
        return rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.');
    }

    private static function escape(string $text): string
    {
        $encoded = @mb_convert_encoding($text, 'Windows-1252', 'UTF-8');

        if ($encoded === false || $encoded === '') {
            $encoded = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $encoded);
    }
}
