<?php

namespace Tests\Feature;

use App\Filament\Resources\GhgSubmissions\Pages\ListGhgSubmissions;
use App\Filament\Resources\GhgSubmissions\Pages\ViewGhgSubmission;
use App\Filament\Widgets\GhgEmissionsByYearChart;
use App\Filament\Widgets\GhgStatsOverviewWidget;
use App\Filament\Widgets\GhgSubmissionsByStatusChart;
use App\Filament\Widgets\GhgTopEmittersTableWidget;
use App\GhgSubmissionStatus;
use App\Models\GhgSubmission;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminGhgSubmissionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
    }

    public function test_admin_can_view_and_search_submissions(): void
    {
        $admin = User::factory()->admin()->create();
        $matchingSubmission = GhgSubmission::factory()->create([
            'code' => 'GHG-2026-TIMTHAY',
            'company_name' => 'Công ty Xanh Sài Gòn',
            'tax_id' => '0311111111',
        ]);
        $otherSubmission = GhgSubmission::factory()->create([
            'code' => 'GHG-2026-KHONGTIM',
            'company_name' => 'Doanh nghiệp Khác',
            'tax_id' => '0322222222',
        ]);

        $this->actingAs($admin);

        Livewire::test(ListGhgSubmissions::class)
            ->searchTable('0311111111')
            ->assertCanSeeTableRecords([$matchingSubmission])
            ->assertCanNotSeeTableRecords([$otherSubmission]);
    }

    public function test_admin_can_view_submission_details(): void
    {
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create([
            'company_name' => 'Công ty Chi Tiết',
            'tax_id' => '0399999999',
        ]);

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.ghg-submissions.view', $submission))
            ->assertOk()
            ->assertSee('Công ty Chi Tiết')
            ->assertSee('0399999999')
            ->assertSee('contact@example.com');
    }

    public function test_admin_can_update_submission_status_with_review_audit(): void
    {
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create();

        $this->actingAs($admin);

        Livewire::test(ViewGhgSubmission::class, ['record' => $submission->getRouteKey()])
            ->callAction('updateStatus', [
                'status' => GhgSubmissionStatus::Completed->value,
                'review_note' => 'Đã kiểm tra đủ số liệu.',
            ])
            ->assertHasNoFormErrors();

        $submission->refresh();

        $this->assertSame(GhgSubmissionStatus::Completed, $submission->status);
        $this->assertSame('Đã kiểm tra đủ số liệu.', $submission->review_note);
        $this->assertTrue($submission->reviewer->is($admin));
        $this->assertNotNull($submission->reviewed_at);
    }

    public function test_invalid_submission_status_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create();

        $this->actingAs($admin);

        Livewire::test(ViewGhgSubmission::class, ['record' => $submission->getRouteKey()])
            ->callAction('updateStatus', [
                'status' => 'deleted',
            ])
            ->assertHasFormErrors(['status']);

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
            ->get(route('admin.ghg-submissions.report.download', $submission))
            ->assertOk()
            ->assertDownload('bao-cao.pdf');
    }

    public function test_admin_can_download_submission_excel_from_internal_route(): void
    {
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.ghg-submissions.excel.download', $submission))
            ->assertOk()
            ->assertDownload();
    }

    public function test_non_admin_cannot_download_internal_submission_files(): void
    {
        $user = User::factory()->create();
        $submission = GhgSubmission::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.ghg-submissions.excel.download', $submission))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.ghg-submissions.report.download', $submission))
            ->assertForbidden();
    }

    public function test_admin_can_filter_submissions_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        $completedSubmission = GhgSubmission::factory()->create([
            'status' => GhgSubmissionStatus::Completed,
        ]);
        $submittedSubmission = GhgSubmission::factory()->create([
            'status' => GhgSubmissionStatus::Submitted,
        ]);

        $this->actingAs($admin);

        Livewire::test(ListGhgSubmissions::class)
            ->filterTable('status', GhgSubmissionStatus::Completed->value)
            ->assertCanSeeTableRecords([$completedSubmission])
            ->assertCanNotSeeTableRecords([$submittedSubmission]);
    }

    public function test_admin_detail_escapes_submitted_company_content(): void
    {
        $admin = User::factory()->admin()->create();
        $submission = GhgSubmission::factory()->create([
            'company_name' => '<script>alert("xss")</script>',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('filament.admin.resources.ghg-submissions.view', $submission));

        $response->assertOk()
            ->assertSee('&lt;script&gt;alert', false)
            ->assertDontSee('<script>alert("xss")</script>', false);
    }

    public function test_viewer_role_can_view_submissions_but_cannot_review_or_manage_users(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $viewer = User::factory()->create(['is_admin' => true]);
        $viewer->assignRole('viewer');
        $submission = GhgSubmission::factory()->create();

        $this->actingAs($viewer)
            ->get(route('filament.admin.resources.ghg-submissions.view', $submission))
            ->assertOk();

        $this->actingAs($viewer)
            ->get(route('filament.admin.resources.users.index'))
            ->assertForbidden();

        $this->assertFalse($viewer->can('update', $submission));
    }

    public function test_super_admin_role_can_access_user_management_and_dashboard_widgets(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $superAdmin = User::factory()->create(['is_admin' => true]);
        $superAdmin->assignRole('super_admin');
        GhgSubmission::factory()->create();

        $this->actingAs($superAdmin)
            ->get(route('filament.admin.pages.dashboard'))
            ->assertOk();

        Livewire::actingAs($superAdmin);

        Livewire::test(GhgStatsOverviewWidget::class)
            ->assertSee('Tổng hồ sơ tiếp nhận')
            ->assertSee('Tổng phát thải ghi nhận');

        Livewire::test(GhgSubmissionsByStatusChart::class)->assertOk();
        Livewire::test(GhgEmissionsByYearChart::class)->assertOk();
        Livewire::test(GhgTopEmittersTableWidget::class)->assertOk();

        $this->actingAs($superAdmin)
            ->get(route('filament.admin.resources.users.index'))
            ->assertOk();
    }
}
