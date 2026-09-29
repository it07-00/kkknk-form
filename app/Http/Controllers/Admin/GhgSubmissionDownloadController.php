<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GhgSubmission;
use App\Services\GhgSubmissionExcelExporter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GhgSubmissionDownloadController extends Controller
{
    public function excel(
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

    public function report(GhgSubmission $submission): StreamedResponse
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
