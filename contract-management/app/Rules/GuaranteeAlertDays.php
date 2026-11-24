<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class GuaranteeAlertDays implements ValidationRule
{
    public function __construct(protected ?string $guaranteeEndDate)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '' || ! $this->guaranteeEndDate) {
            return;
        }

        $daysAvailable = (int) now()
            ->startOfDay()
            ->diffInDays(Carbon::parse($this->guaranteeEndDate)->startOfDay(), false);

        // An already-expired guarantee cannot schedule an alert; keep the record editable.
        if ($daysAvailable < 0) {
            return;
        }

        if ((int) $value > $daysAvailable) {
            $fail(__('performance_guarantee.validation_alert_days_exceed', ['days' => $daysAvailable]));
        }
    }
}
