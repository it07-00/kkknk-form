<?php

namespace Tests\Feature;

use App\Models\GhgSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GhgReportFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_report_can_be_uploaded_and_downloaded_from_signed_receipt_link(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()
            ->createWithContent('bao-cao-giam-nhe.pdf', "%PDF-1.4\nBao cao giam nhe")
            ->mimeType('application/pdf');

        $response = $this->post(route('form.submit'), [
            'formData' => json_encode($this->validFormData(), JSON_THROW_ON_ERROR),
            'mitigation_report_file' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertCreated()
            ->assertJsonPath('receipt.report_file_name', 'bao-cao-giam-nhe.pdf')
            ->assertJsonStructure(['receipt' => ['report_file_url']]);

        $submission = GhgSubmission::query()->sole();

        $this->assertSame('bao-cao-giam-nhe.pdf', $submission->mitigation_report_original_name);
        $this->assertSame('application/pdf', $submission->mitigation_report_mime_type);
        $this->assertNotNull($submission->mitigation_report_path);
        Storage::disk('local')->assertExists($submission->mitigation_report_path);

        $this->get($response->json('receipt.report_file_url'))
            ->assertOk()
            ->assertDownload('bao-cao-giam-nhe.pdf');
    }

    public function test_report_download_returns_403_without_a_valid_signature(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()
            ->createWithContent('bao-cao.pdf', "%PDF-1.4\nBao cao")
            ->mimeType('application/pdf');

        $response = $this->post(route('form.submit'), [
            'formData' => json_encode($this->validFormData(), JSON_THROW_ON_ERROR),
            'mitigation_report_file' => $file,
        ], ['Accept' => 'application/json']);
        $unsignedPath = (string) parse_url($response->json('receipt.report_file_url'), PHP_URL_PATH);

        $this->get($unsignedPath)->assertForbidden();
    }

    public function test_executable_report_file_is_rejected_and_not_stored(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()
            ->createWithContent('bao-cao.php', '<?php echo "unsafe";')
            ->mimeType('application/x-httpd-php');

        $response = $this->post(route('form.submit'), [
            'formData' => json_encode($this->validFormData(), JSON_THROW_ON_ERROR),
            'mitigation_report_file' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertUnprocessable()
            ->assertInvalid([
                'mitigation_report_file' => 'File báo cáo chỉ chấp nhận định dạng PDF, Word hoặc Excel.',
            ]);
        $this->assertDatabaseCount('ghg_submissions', 0);
        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    public function test_report_file_larger_than_ten_megabytes_is_rejected(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('bao-cao.pdf', 10241, 'application/pdf');

        $response = $this->post(route('form.submit'), [
            'formData' => json_encode($this->validFormData(), JSON_THROW_ON_ERROR),
            'mitigation_report_file' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertUnprocessable()
            ->assertInvalid([
                'mitigation_report_file' => 'File báo cáo không được lớn hơn 10 MB.',
            ]);
        $this->assertDatabaseCount('ghg_submissions', 0);
        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    public function test_php_content_with_a_pdf_extension_is_rejected(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()
            ->createWithContent('bao-cao.pdf', '<?php echo "unsafe";')
            ->mimeType('application/x-httpd-php');

        $response = $this->post(route('form.submit'), [
            'formData' => json_encode($this->validFormData(), JSON_THROW_ON_ERROR),
            'mitigation_report_file' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertUnprocessable()
            ->assertInvalid([
                'mitigation_report_file' => 'File báo cáo chỉ chấp nhận định dạng PDF, Word hoặc Excel.',
            ]);
        $this->assertDatabaseCount('ghg_submissions', 0);
        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    /**
     * @return array<string, mixed>
     */
    private function validFormData(): array
    {
        return [
            'company' => [
                'name' => 'Công ty TNHH Thử Nghiệm Xanh',
                'tax_code' => '0312345678',
                'address' => '123 Nguyễn Huệ, TP.HCM',
                'industry' => 'Sản xuất xanh',
                'email' => 'contact@example.com',
                'legal_representative' => ['name' => 'Nguyễn Văn A', 'phone' => '0901000000'],
                'technical_contact' => ['name' => 'Trần Thị B', 'phone' => '0902000000'],
            ],
            'reporting_years' => ['2024'],
            'inventory' => [
                '2024' => [
                    'has_boiler' => false,
                    'has_cooling' => false,
                    'scope1_sources' => [[
                        'id' => 'source-1',
                        'source_type' => 'Đốt nhiên liệu di động',
                        'fuel_type' => 'Dầu DO',
                        'quantity' => 10,
                        'unit' => 'lít',
                    ]],
                    'grid_electricity_kwh' => 50000,
                    'solar_electricity_kwh' => 0,
                    'report_method' => 'Chưa có báo cáo',
                    'report_url' => '',
                ],
            ],
            'mitigation' => [
                'implemented' => false,
                'report_url' => '',
            ],
            'confirmation' => true,
        ];
    }
}
