<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Suspended = 'suspended';
    case InProgress = 'in_progress';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::UnderReview => 'Under Review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Suspended => 'Suspended',
            self::InProgress => 'In Progress',
        };
    }

    public function labelAr(): string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::UnderReview => 'تحت المراجعة',
            self::Approved => 'مقبول',
            self::Rejected => 'مرفوض',
            self::Suspended => 'معلق',
            self::InProgress => 'قيد المعالجة',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::UnderReview => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Suspended => 'gray',
            self::InProgress => 'primary',
        };
    }
}
