<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GhgSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'company_name',
        'tax_id',
        'reporting_years',
        'data',
        'status',
        'ip_address',
    ];

    protected $casts = [
        'reporting_years' => 'array',
        'data' => 'array',
    ];
}
