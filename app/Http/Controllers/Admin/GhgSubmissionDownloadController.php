<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GhgSubmission;
use App\Services\GhgSubmissionExcelExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GhgSubmissionDownloadController extends Controller
{
    public function excel(
        Request $request,
        GhgSubmission $submission,
        GhgSubmissionExcelExporter $exporter
    ): BinaryFileResponse {
        abort_unless($request->user()?->hasPermissionOrLegacyAdmin('submissions.export'), 403);

        $path = $exporter->export($submission);

        return response()->download(
            $path,
            $exporter->downloadName($submission),
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend();
    }

    public function report(Request $request, GhgSubmission $submission): StreamedResponse
    {
        abort_unless($request->user()?->hasPermissionOrLegacyAdmin('submissions.download_report'), 403);

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
