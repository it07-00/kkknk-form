<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GhgReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('BẢNG CUNG CẤP SỐ LIỆU KIỂM KÊ KHÍ NHÀ KÍNH VÀ KẾ HOẠCH GIẢM NHẸ');
        $response->assertSee('SỞ CÔNG THƯƠNG TP. HỒ CHÍ MINH');
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
                        'has_scope1' => false,
                        'has_boiler' => false,
                        'has_cooling' => false,
                        'scope1_sources' => [],
                        'grid_electricity_kwh' => 50000,
                        'solar_electricity_kwh' => 0,
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
