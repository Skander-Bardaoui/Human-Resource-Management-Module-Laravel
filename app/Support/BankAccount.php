<?php

declare(strict_types=1);

namespace App\Support;

final class BankAccount
{
    /**
     * @return array{0: string, 1: string}
     */
    public static function parse(?string $account): array
    {
        $account = trim((string) $account);

        if ($account === '' || strtolower($account) === 'non renseigné') {
            return ['—', '—'];
        }

        $parts = preg_split('/\s+/', $account, 2);
        $short = strtoupper($parts[0] ?? '');
        $bank = match ($short) {
            'AMEN' => 'Amen Bank',
            'BIAT' => 'BIAT',
            'UIB' => 'UIB',
            'ATB' => 'ATB',
            'BH' => 'BH Bank',
            'STB' => 'STB',
            default => $parts[0] ?? '—',
        };

        return [$bank, $parts[1] ?? '—'];
    }

    public static function isUsable(?string $rib): bool
    {
        $rib = trim((string) $rib);

        return $rib !== '' && $rib !== '—';
    }
}
