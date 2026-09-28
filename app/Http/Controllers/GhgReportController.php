<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGhgSubmissionRequest;
use App\Models\GhgSubmission;
use App\Services\GhgSubmissionExcelExporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
    public function store(StoreGhgSubmissionRequest $request): JsonResponse
    {
        $formData = $request->validated('formData');
        $code = 'GHG-'.now()->format('Y').'-'.Str::upper(Str::random(10));

        $submission = GhgSubmission::create([
            'code' => $code,
            'company_name' => $formData['company']['name'],
            'tax_id' => $formData['company']['tax_code'],
            'reporting_years' => $formData['reporting_years'],
            'data' => $formData,
            'status' => 'submitted',
            'ip_address' => $request->ip(),
        ]);

        $excelUrl = URL::temporarySignedRoute(
            'submissions.excel',
            now()->addDay(),
            ['submission' => $submission->code]
        );

        return response()->json([
            'success' => true,
            'message' => 'Hồ sơ đã được lưu trữ thành công vào hệ thống.',
            'receipt' => [
                'code' => $submission->code,
                'time' => $submission->created_at->format('d/m/Y H:i'),
                'company' => $submission->company_name,
                'tax_id' => $submission->tax_id,
                'excel_url' => $excelUrl,
            ],
        ], 201);
    }

    public function downloadExcel(
        GhgSubmission $submission,
        GhgSubmissionExcelExporter $exporter
    ): BinaryFileResponse {
        $path = $exporter->export($submission);

        return response()->download(
            $path,
            $exporter->downloadName($submission),
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend();
    }
}
