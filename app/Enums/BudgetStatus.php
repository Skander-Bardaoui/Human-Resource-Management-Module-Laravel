<?php

declare(strict_types=1);

namespace App\Enums;

use App\Support\Money;

enum BudgetStatus: string
{
    case Undefined = 'undefined';
    case Within = 'within';
    case Over = 'over';

    public static function fromAmounts(Money $gross, Money $budget): self
    {
        if ($budget->isZero()) {
            return self::Undefined;
        }

        return $gross->greaterThan($budget) ? self::Over : self::Within;
    }

    public function label(): string
    {
        return match ($this) {
            self::Undefined => 'Budget à définir',
            self::Within => 'Conforme',
            self::Over => 'Dépassement',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Undefined => 'neutral',
            self::Within => 'success',
            self::Over => 'error',
        };
    }
}
