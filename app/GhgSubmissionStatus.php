<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum GhgSubmissionStatus: string implements HasColor, HasLabel
{
    case Submitted = 'submitted';
    case Reviewing = 'reviewing';
    case NeedsRevision = 'needs_revision';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Mới tiếp nhận',
            self::Reviewing => 'Đang xử lý',
            self::NeedsRevision => 'Cần bổ sung',
            self::Completed => 'Đã hoàn tất',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Submitted => 'info',
            self::Reviewing => 'warning',
            self::NeedsRevision => 'danger',
            self::Completed => 'success',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Submitted => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            self::Reviewing => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::NeedsRevision => 'bg-red-50 text-red-700 ring-red-600/20',
            self::Completed => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        };
    }
}
