<?php

namespace Tests\Feature;

use App\GhgSubmissionStatus;
use App\Models\GhgSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminGhgSubmissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_search_submissions(): void
    {
        $admin = User::factory()->admin()->create();
        GhgSubmission::factory()->create([
            'code' => 'GHG-2026-TIMTHAY',
            'company_name' => 'Công ty Xanh Sài Gòn',
            'tax_id' => '0311111111',
        ]);
        GhgSubmission::factory()->create([
            'code' => 'GHG-2026-KHONGTIM',
            'company_name' => 'Doanh nghiệp Khác',
            'tax_id' => '0322222222',
        ]);

        $this->actingAs($admin)
            ->get('/admin/submissions?search=0311111111')
            ->assertOk()
            ->assertSee('Công ty Xanh Sài Gòn')
            ->assertDontSee('Doanh nghiệp Khác');
    }

    public function test_admin_can_view_submission_details(): void
    {
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create([
            'company_name' => 'Công ty Chi Tiết',
            'tax_id' => '0399999999',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.show', $submission))
            ->assertOk()
            ->assertSee('Công ty Chi Tiết')
            ->assertSee('0399999999')
            ->assertSee('contact@example.com');
    }

    public function test_admin_can_update_submission_status_with_review_audit(): void
    {
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.submissions.status.update', $submission), [
                'status' => GhgSubmissionStatus::Completed->value,
            ])->assertRedirect(route('admin.submissions.show', $submission))
            ->assertSessionHas('status', 'Đã cập nhật trạng thái hồ sơ.');

        $submission->refresh();

        $this->assertSame(GhgSubmissionStatus::Completed, $submission->status);
        $this->assertTrue($submission->reviewer->is($admin));
        $this->assertNotNull($submission->reviewed_at);
    }

    public function test_invalid_submission_status_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create();

        $this->actingAs($admin)
            ->from(route('admin.submissions.show', $submission))
            ->patch(route('admin.submissions.status.update', $submission), [
                'status' => 'deleted',
            ])->assertRedirect(route('admin.submissions.show', $submission))
            ->assertInvalid([
                'status' => 'Trạng thái hồ sơ không hợp lệ.',
            ]);

        $this->assertSame(GhgSubmissionStatus::Submitted, $submission->fresh()->status);
    }

    public function test_admin_can_download_private_report_without_public_signed_link(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('reports/bao-cao.pdf', '%PDF-1.4 report');
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create([
            'mitigation_report_path' => 'reports/bao-cao.pdf',
            'mitigation_report_original_name' => 'bao-cao.pdf',
            'mitigation_report_mime_type' => 'application/pdf',
            'mitigation_report_size' => 15,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.report.download', $submission))
            ->assertOk()
            ->assertDownload('bao-cao.pdf');
    }

    public function test_non_admin_cannot_change_submission_status(): void
    {
        $user = User::factory()->create();
        $submission = GhgSubmission::factory()->create();

        $this->actingAs($user)
            ->patch(route('admin.submissions.status.update', $submission), [
                'status' => GhgSubmissionStatus::Completed->value,
            ])->assertForbidden();

        $this->assertSame(GhgSubmissionStatus::Submitted, $submission->fresh()->status);
    }

    public function test_admin_detail_escapes_submitted_company_content(): void
    {
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create([
            'company_name' => '<script>alert("xss")</script>',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.submissions.show', $submission));

        $response->assertOk()
            ->assertSee('&lt;script&gt;alert', false)
            ->assertDontSee('<script>alert("xss")</script>', false);
    }

    public function test_invalid_index_status_filter_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/submissions?status=deleted')
            ->assertSessionHasErrors('status');
    }
}
