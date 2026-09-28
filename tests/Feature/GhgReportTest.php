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

        $response->assertStatus(200);
        $response->assertSee('BẢNG CUNG CẤP SỐ LIỆU KIỂM KÊ KHÍ NHÀ KÍNH VÀ KẾ HOẠCH GIẢM NHẸ');
        $response->assertSee('SỞ CÔNG THƯƠNG TP. HỒ CHÍ MINH');
    }

    public function test_form_submission_stores_data_in_database(): void
    {
        $payload = [
            'formData' => [
                'company' => [
                    'name' => 'Công ty TNHH Thử Nghiệm Xanh',
                    'tax_id' => '0312345678',
                ],
                'reporting_years' => [2024, 2025],
                'scope1' => [
                    '2024' => [],
                    '2025' => [],
                ],
                'scope2' => [
                    '2024' => ['grid_kwh' => 50000],
                    '2025' => ['grid_kwh' => 52000],
                ],
                'mitigation' => [
                    'implemented' => true,
                ],
                'confirmation' => true,
            ],
        ];

        $response = $this->postJson('/api/submissions', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('ghg_submissions', [
            'company_name' => 'Công ty TNHH Thử Nghiệm Xanh',
            'tax_id' => '0312345678',
        ]);
    }
}
