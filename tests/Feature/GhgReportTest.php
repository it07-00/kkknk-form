<?php

namespace Tests\Feature;

use App\Models\GhgSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GhgReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('BẢNG CUNG CẤP SỐ LIỆU KIỂM KÊ KHÍ NHÀ KÍNH VÀ KẾ HOẠCH GIẢM NHẸ');
        $response->assertSee('SỞ CÔNG THƯƠNG TP. HỒ CHÍ MINH');

        $content = $response->getContent();

        $this->assertSame(1, substr_count($content, '<form'));
        $this->assertStringContainsString('@submit.prevent="handleNext()"', $content);
        $response->assertDontSee('Dữ liệu mẫu từ Sở Công Thương');
        $response->assertDontSee('Nạp số liệu mẫu tự động');
        $response->assertDontSee('Vui lòng kiểm tra và hoàn thành các thông tin chưa hợp lệ bên dưới');
        $response->assertDontSee('doanh nghiệp có phát sinh nguồn phát thải thuộc Phạm vi 1 không?');
        $response->assertDontSee('Không có nguồn phát thải');
    }

    public function test_form_submission_stores_data_in_database(): void
    {
        $payload = $this->validPayload();

        $response = $this->postJson('/api/submissions', $payload);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('receipt.tax_id', '0312345678')
            ->assertJsonStructure(['receipt' => ['code', 'time', 'company', 'tax_id', 'excel_url']]);

        $this->assertDatabaseHas('ghg_submissions', [
            'company_name' => 'Công ty TNHH Thử Nghiệm Xanh',
            'tax_id' => '0312345678',
        ]);
        $this->assertTrue(GhgSubmission::query()->sole()->data['inventory']['2024']['has_scope1']);
    }

    public function test_submission_rejects_scope_one_without_a_source_even_when_client_sends_false(): void
    {
        $payload = $this->validPayload();
        $payload['formData']['inventory']['2024']['has_scope1'] = false;
        $payload['formData']['inventory']['2024']['scope1_sources'] = [];

        $response = $this->postJson('/api/submissions', $payload);

        $response->assertUnprocessable()
            ->assertInvalid([
                'formData.inventory.2024.scope1_sources' => 'Vui lòng khai báo ít nhất một nguồn phát thải Phạm vi 1 cho năm 2024.',
            ]);
        $this->assertDatabaseCount('ghg_submissions', 0);
    }

    public function test_submission_rejects_incomplete_boiler_and_refrigeration_details(): void
    {
        $payload = $this->validPayload();
        $payload['formData']['inventory']['2024']['has_scope1'] = true;
        $payload['formData']['inventory']['2024']['has_boiler'] = true;
        $payload['formData']['inventory']['2024']['has_cooling'] = true;
        $payload['formData']['inventory']['2024']['boiler'] = [
            'capacity' => '',
            'fuel' => 'Khác',
            'fuel_other' => '',
            'consumption' => null,
            'unit' => '',
        ];
        $payload['formData']['inventory']['2024']['refrigeration'] = [
            'equipment' => 'Khác',
            'equipment_other' => '',
            'capacity' => '',
            'gas_type' => 'Khác',
            'gas_type_other' => '',
            'full_charge_kg' => null,
            'recharge_kg' => null,
        ];
        $payload['formData']['inventory']['2024']['scope1_sources'] = [
            [
                'id' => 'source-1',
                'source_type' => 'Đốt nhiên liệu cố định',
                'fuel_type' => 'Dầu DO',
                'quantity' => 100,
                'unit' => 'lít',
            ],
        ];

        $response = $this->postJson('/api/submissions', $payload);

        $response->assertUnprocessable()
            ->assertInvalid([
                'formData.inventory.2024.boiler.capacity',
                'formData.inventory.2024.boiler.fuel_other',
                'formData.inventory.2024.boiler.consumption',
                'formData.inventory.2024.boiler.unit',
                'formData.inventory.2024.refrigeration.equipment_other',
                'formData.inventory.2024.refrigeration.capacity',
                'formData.inventory.2024.refrigeration.gas_type_other',
                'formData.inventory.2024.refrigeration.full_charge_kg',
            ]);
        $this->assertDatabaseCount('ghg_submissions', 0);
    }

    public function test_submission_rejects_incomplete_custom_scope_one_source(): void
    {
        $payload = $this->validPayload();
        $payload['formData']['inventory']['2024']['has_scope1'] = true;
        $payload['formData']['inventory']['2024']['scope1_sources'] = [
            [
                'id' => 'source-1',
                'source_type' => 'Khác',
                'source_type_other' => '',
                'fuel_type' => 'Khác',
                'fuel_type_other' => '',
                'quantity' => null,
                'unit' => 'Khác',
                'unit_other' => '',
            ],
        ];

        $response = $this->postJson('/api/submissions', $payload);

        $response->assertUnprocessable()
            ->assertInvalid([
                'formData.inventory.2024.scope1_sources.0.source_type_other',
                'formData.inventory.2024.scope1_sources.0.fuel_type_other',
                'formData.inventory.2024.scope1_sources.0.quantity',
                'formData.inventory.2024.scope1_sources.0.unit_other',
            ]);
        $this->assertDatabaseCount('ghg_submissions', 0);
    }

    public function test_submission_requires_a_valid_report_link_when_link_method_is_selected(): void
    {
        $payload = $this->validPayload();
        $payload['formData']['inventory']['2024']['report_method'] = 'Dán link báo cáo';
        $payload['formData']['inventory']['2024']['report_url'] = '';

        $response = $this->postJson('/api/submissions', $payload);

        $response->assertUnprocessable()
            ->assertInvalid([
                'formData.inventory.2024.report_url' => 'Vui lòng cung cấp đường dẫn báo cáo kiểm kê cho năm 2024.',
            ]);
        $this->assertDatabaseCount('ghg_submissions', 0);
    }

    public function test_submission_normalizes_scope_one_to_present_when_client_sends_false(): void
    {
        $payload = $this->validPayload();
        $payload['formData']['inventory']['2024']['has_scope1'] = false;

        $response = $this->postJson('/api/submissions', $payload);

        $response->assertCreated();
        $this->assertTrue(GhgSubmission::query()->sole()->data['inventory']['2024']['has_scope1']);
    }

    public function test_submission_rejects_non_http_report_links(): void
    {
        $payload = $this->validPayload();
        $payload['formData']['inventory']['2024']['report_method'] = 'Dán link báo cáo';
        $payload['formData']['inventory']['2024']['report_url'] = 'ftp://example.com/report.xlsx';

        $response = $this->postJson('/api/submissions', $payload);

        $response->assertUnprocessable()
            ->assertInvalid([
                'formData.inventory.2024.report_url' => 'Đường dẫn báo cáo kiểm kê phải bắt đầu bằng http:// hoặc https://.',
            ]);
        $this->assertDatabaseCount('ghg_submissions', 0);
    }

    public function test_submission_requires_details_when_mitigation_has_been_implemented(): void
    {
        $payload = $this->validPayload();
        $payload['formData']['mitigation']['implemented'] = true;

        $response = $this->postJson('/api/submissions', $payload);

        $response->assertUnprocessable()
            ->assertInvalid([
                'formData.mitigation.implemented_measures' => 'Vui lòng mô tả các biện pháp giảm nhẹ đã thực hiện.',
            ]);
        $this->assertDatabaseCount('ghg_submissions', 0);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'formData' => [
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
                ],
                'confirmation' => true,
            ],
        ];
    }
}
