<?php

namespace App\Http\Controllers;

use App\Models\GhgSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GhgReportController extends Controller
{
    /**
     * Display the GHG Inventory Submission Form.
     */
    public function index(): View
    {
        return view('forms.greenhouse-gas');
    }

    /**
     * Store a newly submitted GHG inventory record.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'formData' => ['required', 'array'],
            'formData.company.name' => ['required', 'string'],
            'formData.company.tax_id' => ['nullable', 'string'],
            'formData.reporting_years' => ['required', 'array'],
        ]);

        $randomSuffix = random_int(100000, 999999);
        $code = 'GHG-2026-' . $randomSuffix;

        $submission = GhgSubmission::create([
            'code' => $code,
            'company_name' => $validated['formData']['company']['name'] ?? 'Không xác định',
            'tax_id' => $validated['formData']['company']['tax_id'] ?? null,
            'reporting_years' => $validated['formData']['reporting_years'] ?? [],
            'data' => $validated['formData'],
            'status' => 'submitted',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hồ sơ đã được lưu trữ thành công vào hệ thống.',
            'receipt' => [
                'code' => $submission->code,
                'time' => $submission->created_at->format('d/m/Y H:i'),
                'company' => $submission->company_name,
                'tax_id' => $submission->tax_id,
            ],
        ]);
    }
}
