<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGhgSubmissionRequest;
use App\Models\GhgSubmission;
use App\Services\GhgSubmissionExcelExporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

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
        $reportFile = $request->file('mitigation_report_file');
        $reportPath = null;

        try {
            if ($reportFile !== null) {
                $reportPath = $reportFile->store("ghg-submissions/{$code}/reports", 'local');

                if ($reportPath === false) {
                    throw new \RuntimeException('Không thể lưu file báo cáo.');
                }
            }

            $submission = DB::transaction(fn (): GhgSubmission => GhgSubmission::create([
                'code' => $code,
                'company_name' => $formData['company']['name'],
                'tax_id' => $formData['company']['tax_code'],
                'reporting_years' => $formData['reporting_years'],
                'data' => $formData,
                'mitigation_report_path' => $reportPath,
                'mitigation_report_original_name' => $reportFile === null
                    ? null
                    : Str::limit(str_replace(['\\', '/'], '_', $reportFile->getClientOriginalName()), 255, ''),
                'mitigation_report_mime_type' => $reportFile?->getMimeType(),
                'mitigation_report_size' => $reportFile?->getSize(),
                'status' => 'submitted',
                'ip_address' => $request->ip(),
            ]));
        } catch (Throwable $exception) {
            if (is_string($reportPath)) {
                Storage::disk('local')->delete($reportPath);
            }

            throw $exception;
        }

        $excelUrl = URL::temporarySignedRoute(
            'submissions.excel',
            now()->addDay(),
            ['submission' => $submission->code]
        );
        $reportFileUrl = $submission->mitigation_report_path === null
            ? null
            : URL::temporarySignedRoute(
                'submissions.mitigation-report',
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
                'report_file_url' => $reportFileUrl,
                'report_file_name' => $submission->mitigation_report_original_name,
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

    public function downloadMitigationReport(GhgSubmission $submission): StreamedResponse
    {
        $path = $submission->mitigation_report_path;

        abort_if($path === null || Storage::disk('local')->missing($path), 404);

        return Storage::disk('local')->download(
            $path,
            $submission->mitigation_report_original_name ?? 'bao-cao-giam-nhe',
            [
                'Content-Type' => $submission->mitigation_report_mime_type ?? 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}
