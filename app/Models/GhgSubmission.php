<?php

namespace App\Models;

use App\GhgSubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GhgSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'company_name',
        'tax_id',
        'reporting_years',
        'data',
        'mitigation_report_path',
        'mitigation_report_original_name',
        'mitigation_report_mime_type',
        'mitigation_report_size',
        'status',
        'reviewed_by',
        'reviewed_at',
        'ip_address',
    ];

    protected $casts = [
        'reporting_years' => 'array',
        'data' => 'array',
        'status' => GhgSubmissionStatus::class,
        'reviewed_at' => 'datetime',
    ];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
