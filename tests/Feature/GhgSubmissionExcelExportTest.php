<?php

namespace Tests\Feature;

use App\Models\GhgSubmission;
use App\Services\GhgSubmissionExcelExporter;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use ZipArchive;

class GhgSubmissionExcelExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_exporter_exports_saved_submission_using_excel_template(): void
    {
        $this->postJson(route('form.submit'), $this->validPayload())->assertCreated();
        $submission = GhgSubmission::query()->sole();
        $path = app(GhgSubmissionExcelExporter::class)->export($submission);

        $archive = new ZipArchive;
        $this->assertTrue($archive->open($path) === true);

        $inventoryXml = (string) $archive->getFromName('xl/worksheets/sheet1.xml');
        $inventoryValues = $this->worksheetValues($inventoryXml);
        $mitigationValues = $this->worksheetValues((string) $archive->getFromName('xl/worksheets/sheet2.xml'));
        $archive->close();

        $this->assertSame('Công ty TNHH Takigawa Việt Nam', $inventoryValues['B6']);
        $this->assertSame('1', $inventoryValues['A6']);
        $this->assertSame('Năm 2024', $inventoryValues['C6']);
        $this->assertSame('3701858627', $inventoryValues['F6']);
        $this->assertSame('3000000', $inventoryValues['I6']);
        $this->assertSame('3 tấn hơi/giờ', $inventoryValues['K6']);
        $this->assertSame('Sinh khối', $inventoryValues['L6']);
        $this->assertSame('500 tấn/năm', $inventoryValues['M6']);
        $this->assertArrayNotHasKey('S6', $inventoryValues);
        $this->assertArrayNotHasKey('T6', $inventoryValues);
        $this->assertSame('2000', $inventoryValues['X6']);
        $this->assertSame('1200', $inventoryValues['Y6']);
        $this->assertSame('3200', $inventoryValues['Z6']);
        $this->assertSame('Kê khai trực tiếp theo hóa đơn', $inventoryValues['AB6']);
        $this->assertSame('https://example.com/bao-cao-2024', $inventoryValues['AC6']);

        $this->assertSame('5 tấn hơi/giờ', $inventoryValues['K7']);
        $this->assertSame('Dầu DO', $inventoryValues['L7']);
        $this->assertSame('700 lít/năm', $inventoryValues['M7']);
        $this->assertArrayNotHasKey('A7', $inventoryValues);
        $this->assertArrayNotHasKey('B7', $inventoryValues);
        $this->assertArrayNotHasKey('C7', $inventoryValues);
        $this->assertArrayNotHasKey('X7', $inventoryValues);

        $this->assertSame('Máy lạnh', $inventoryValues['N8']);
        $this->assertArrayNotHasKey('T8', $inventoryValues);
        $this->assertArrayNotHasKey('W8', $inventoryValues);
        $this->assertSame('2', $inventoryValues['AA8']);

        $this->assertSame('Chiller', $inventoryValues['N9']);
        $this->assertArrayNotHasKey('T9', $inventoryValues);
        $this->assertSame('3', $inventoryValues['AA9']);

        $this->assertSame('Đốt nhiên liệu di động', $inventoryValues['S10']);
        $this->assertSame('Dầu DO', $inventoryValues['T10']);
        $this->assertSame('15000', $inventoryValues['U10']);
        $this->assertSame('lít', $inventoryValues['V10']);
        $this->assertSame('Phương tiện vận tải của Công ty', $inventoryValues['W10']);

        $this->assertSame('2', $inventoryValues['A11']);
        $this->assertSame('Năm 2025', $inventoryValues['C11']);
        $this->assertArrayNotHasKey('B11', $inventoryValues);
        $this->assertArrayNotHasKey('C15', $inventoryValues);
        $this->assertSame('Nguồn phát thải Phạm vi 1 khác', $inventoryValues['S4']);
        $this->assertSame('Loại nguồn', $inventoryValues['S5']);
        $this->assertSame('Nhiên liệu / chất sử dụng', $inventoryValues['T5']);
        $this->assertSame('Số lượng', $inventoryValues['U5']);
        $this->assertSame('Đơn vị', $inventoryValues['V5']);
        $this->assertSame('Ghi chú', $inventoryValues['W5']);
        $this->assertSame('Tổng phát thải theo năm (tCO₂e)', $inventoryValues['X4']);
        $this->assertSame('Phạm vi 1', $inventoryValues['X5']);
        $this->assertSame('Phạm vi 2', $inventoryValues['Y5']);
        $this->assertSame('Tổng cộng', $inventoryValues['Z5']);
        $this->assertSame('Thông tin bổ sung', $inventoryValues['AA4']);
        $this->assertSame('Lượng nạp bổ sung (kg)', $inventoryValues['AA5']);
        $this->assertSame('Phương thức báo cáo', $inventoryValues['AB5']);
        $this->assertSame('Liên kết báo cáo', $inventoryValues['AC5']);
        $this->assertSame('TỔNG CỘNG', $inventoryValues['B16']);
        $this->assertSame('4000', $inventoryValues['X16']);
        $this->assertSame('2550', $inventoryValues['Y16']);
        $this->assertSame('6550', $inventoryValues['Z16']);
        $this->assertStringContainsString('ref="A4:AC16"', $inventoryXml);
        $this->assertStringContainsString('ref="S4:W4"', $inventoryXml);
        $this->assertStringContainsString('ref="X4:Z4"', $inventoryXml);
        $this->assertStringContainsString('ref="AA4:AC4"', $inventoryXml);
        $this->assertStringContainsString('ref="B6:B15"', $inventoryXml);
        $this->assertStringContainsString('ref="D6:D15"', $inventoryXml);
        $this->assertStringContainsString('ref="G6:G15"', $inventoryXml);
        $this->assertStringContainsString('ref="A6:A10"', $inventoryXml);
        $this->assertStringContainsString('ref="C6:C10"', $inventoryXml);
        $this->assertStringContainsString('ref="I6:I10"', $inventoryXml);
        $this->assertStringContainsString('ref="J6:J10"', $inventoryXml);
        $this->assertStringContainsString('ref="R6:R10"', $inventoryXml);
        $this->assertStringContainsString('ref="X6:X10"', $inventoryXml);
        $this->assertStringContainsString('ref="Y6:Y10"', $inventoryXml);
        $this->assertStringContainsString('ref="Z6:Z10"', $inventoryXml);
        $this->assertStringContainsString('ref="AB6:AB10"', $inventoryXml);
        $this->assertStringContainsString('ref="AC6:AC10"', $inventoryXml);
        $this->assertSame('0.2', $mitigationValues['K4']);

        unlink($path);
    }

    public function test_customer_excel_download_route_is_not_registered(): void
    {
        $this->assertFalse(Route::has('submissions.excel'));
    }

    public function test_submission_returns_422_when_a_selected_year_has_no_inventory(): void
    {
        $payload = $this->validPayload();
        unset($payload['formData']['inventory']['2024']);

        $this->postJson(route('form.submit'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['formData.inventory.2024']);

        $this->assertDatabaseCount('ghg_submissions', 0);
    }

    /**
     * @return array<string, string>
     */
    private function worksheetValues(string $xml): array
    {
        $document = new DOMDocument;
        $this->assertTrue($document->loadXML($xml));

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $values = [];

        foreach ($xpath->query('//x:c') ?: [] as $cell) {
            $reference = $cell->attributes?->getNamedItem('r')?->nodeValue;

            if ($reference === null) {
                continue;
            }

            $inlineText = $xpath->query('x:is/x:t', $cell)?->item(0)?->textContent;
            $numericValue = $xpath->query('x:v', $cell)?->item(0)?->textContent;

            if ($inlineText !== null || $numericValue !== null) {
                $values[$reference] = $inlineText ?? $numericValue;
            }
        }

        return $values;
    }

    public function test_internal_exporter_exports_multiple_submissions_with_summary_row(): void
    {
        $first = GhgSubmission::factory()->create(['company_name' => 'Công ty Một']);
        $second = GhgSubmission::factory()->create(['company_name' => 'Công ty Hai']);

        $path = app(GhgSubmissionExcelExporter::class)->exportMany(collect([$first, $second]));

        $archive = new ZipArchive;
        $this->assertTrue($archive->open($path) === true);
        $mitigationValues = $this->worksheetValues((string) $archive->getFromName('xl/worksheets/sheet2.xml'));
        $archive->close();

        $this->assertSame('1', $mitigationValues['A4']);
        $this->assertSame('2', $mitigationValues['A5']);
        $this->assertSame('TỔNG CỘNG', $mitigationValues['B6']);
    }

    public function test_internal_exporter_neutralizes_excel_formula_injection(): void
    {
        $payload = $this->validPayload();
        $payload['formData']['company']['name'] = '=CMD("calc")';
        $payload['formData']['company']['address'] = '+SUM(1,2)';
        $payload['formData']['mitigation']['plan_2026_2030'] = '@evil';

        $this->postJson(route('form.submit'), $payload)->assertCreated();
        $submission = GhgSubmission::query()->sole();
        $path = app(GhgSubmissionExcelExporter::class)->export($submission);

        $archive = new ZipArchive;
        $this->assertTrue($archive->open($path) === true);

        $inventoryValues = $this->worksheetValues((string) $archive->getFromName('xl/worksheets/sheet1.xml'));
        $mitigationValues = $this->worksheetValues((string) $archive->getFromName('xl/worksheets/sheet2.xml'));
        $archive->close();

        $this->assertSame('\'=CMD("calc")', $inventoryValues['B6']);
        $this->assertSame('\'+SUM(1,2)', $inventoryValues['D6']);
        $this->assertSame('\'@evil', $mitigationValues['G4']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'formData' => [
                'company' => [
                    'name' => 'Công ty TNHH Takigawa Việt Nam',
                    'tax_code' => '3701858627',
                    'address' => 'Số 10, đường số 14, khu công nghiệp VSIP II-A, TP.HCM',
                    'industry' => 'Sản xuất, in ấn, thiết kế bao bì',
                    'email' => 'info@takigawa.vn',
                    'legal_representative' => ['name' => 'Takigawa Hiroshi', 'phone' => '0901000000'],
                    'technical_contact' => ['name' => 'Chị Nguyệt Sương', 'phone' => '0903841777'],
                ],
                'reporting_years' => ['2024', '2025'],
                'inventory' => [
                    '2024' => $this->inventory(3000000, 300000, 1900, 2000, 1200),
                    '2025' => $this->inventory(3400000, 300000, 1850, 2000, 1350),
                ],
                'mitigation' => [
                    'implemented' => true,
                    'plan_2026_2030' => 'Lắp đặt hệ thống điện mặt trời mái nhà để tự dùng.',
                    'implemented_measures' => 'Gắn pin năng lượng mặt trời mái nhà 1 MW.',
                    'planned_reduction_tco2e' => 1000,
                    'actual_reduction_tco2e' => 200,
                ],
                'confirmation' => true,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function inventory(
        int $gridElectricity,
        int $solarElectricity,
        int $energyToe,
        int $scope1Emissions,
        int $scope2Emissions
    ): array {
        return [
            'has_scope1' => true,
            'has_boiler' => true,
            'boilers' => [
                [
                    'id' => 'boiler-1',
                    'capacity' => '3 tấn hơi/giờ',
                    'fuel' => 'Sinh khối',
                    'fuel_other' => '',
                    'consumption' => 500,
                    'unit' => 'tấn/năm',
                ],
                [
                    'id' => 'boiler-2',
                    'capacity' => '5 tấn hơi/giờ',
                    'fuel' => 'Dầu DO',
                    'fuel_other' => '',
                    'consumption' => 700,
                    'unit' => 'lít/năm',
                ],
            ],
            'has_cooling' => true,
            'refrigeration_systems' => [
                [
                    'id' => 'cooling-1',
                    'equipment' => 'Máy lạnh',
                    'equipment_other' => '',
                    'capacity' => '2 HP',
                    'gas_type' => 'R22',
                    'gas_type_other' => '',
                    'full_charge_kg' => 10,
                    'recharge_kg' => 2,
                ],
                [
                    'id' => 'cooling-2',
                    'equipment' => 'Chiller',
                    'equipment_other' => '',
                    'capacity' => '50 HP',
                    'gas_type' => 'R134a',
                    'gas_type_other' => '',
                    'full_charge_kg' => 20,
                    'recharge_kg' => 3,
                ],
            ],
            'scope1_sources' => [[
                'id' => 'source-1',
                'source_type' => 'Đốt nhiên liệu di động',
                'fuel_type' => 'Dầu DO',
                'quantity' => 15000,
                'unit' => 'lít',
                'note' => 'Phương tiện vận tải của Công ty',
            ]],
            'grid_electricity_kwh' => $gridElectricity,
            'solar_electricity_kwh' => $solarElectricity,
            'energy_toe' => $energyToe,
            'scope1_emissions' => $scope1Emissions,
            'scope2_emissions' => $scope2Emissions,
            'report_method' => 'Kê khai trực tiếp theo hóa đơn',
            'report_url' => $gridElectricity === 3000000 ? 'https://example.com/bao-cao-2024' : '',
        ];
    }
}
