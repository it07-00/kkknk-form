<?php

namespace App\Http\Controllers\Admin;

use App\GhgSubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGhgSubmissionStatusRequest;
use App\Models\GhgSubmission;
use App\Services\GhgSubmissionExcelExporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GhgSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(GhgSubmissionStatus::class)],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $status = $filters['status'] ?? null;

        $submissions = GhgSubmission::query()
            ->select(['id', 'code', 'company_name', 'tax_id', 'reporting_years', 'status', 'created_at'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('tax_id', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $statusCounts = GhgSubmission::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('admin.submissions.index', [
            'submissions' => $submissions,
            'statuses' => GhgSubmissionStatus::cases(),
            'statusCounts' => $statusCounts,
            'totalSubmissions' => $statusCounts->sum(),
        ]);
    }

    public function show(GhgSubmission $submission): View
    {
        $submission->load('reviewer:id,name,email');

        return view('admin.submissions.show', [
            'submission' => $submission,
            'statuses' => GhgSubmissionStatus::cases(),
        ]);
    }

    public function updateStatus(
        UpdateGhgSubmissionStatusRequest $request,
        GhgSubmission $submission
    ): RedirectResponse {
        $submission->update([
            'status' => $request->enum('status', GhgSubmissionStatus::class),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('status', 'Đã cập nhật trạng thái hồ sơ.');
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

    public function downloadReport(GhgSubmission $submission): StreamedResponse
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
