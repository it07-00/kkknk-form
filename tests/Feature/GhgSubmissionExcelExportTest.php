<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class GhgSubmissionExcelExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_download_exports_saved_submission_using_excel_template(): void
    {
        $storeResponse = $this->postJson(route('form.submit'), $this->validPayload());
        $excelUrl = $storeResponse->json('receipt.excel_url');

        $response = $this->get($excelUrl);

        $response->assertOk()->assertDownload();

        $archive = new ZipArchive;
        $this->assertTrue($archive->open($response->baseResponse->getFile()->getPathname()) === true);

        $inventoryValues = $this->worksheetValues((string) $archive->getFromName('xl/worksheets/sheet1.xml'));
        $mitigationValues = $this->worksheetValues((string) $archive->getFromName('xl/worksheets/sheet2.xml'));
        $archive->close();

        $this->assertSame('Công ty TNHH Takigawa Việt Nam', $inventoryValues['B6']);
        $this->assertSame('Năm 2024', $inventoryValues['C6']);
        $this->assertSame('3701858627', $inventoryValues['F6']);
        $this->assertSame('3000000', $inventoryValues['I6']);
        $this->assertSame('3 tấn hơi/giờ; 5 tấn hơi/giờ', $inventoryValues['K6']);
        $this->assertSame('Sinh khối; Dầu DO', $inventoryValues['L6']);
        $this->assertSame('500 tấn/năm; 700 lít/năm', $inventoryValues['M6']);
        $this->assertSame('Máy lạnh; Chiller', $inventoryValues['N6']);
        $this->assertSame('2 HP; 50 HP', $inventoryValues['O6']);
        $this->assertSame('R22; R134a', $inventoryValues['P6']);
        $this->assertSame('10; 20', $inventoryValues['Q6']);
        $this->assertSame('2000', $inventoryValues['U6']);
        $this->assertSame('3200', $inventoryValues['W6']);
        $this->assertSame('Năm 2025', $inventoryValues['C7']);
        $this->assertSame('0.2', $mitigationValues['K4']);
    }

    public function test_download_returns_403_without_a_valid_signature(): void
    {
        $storeResponse = $this->postJson(route('form.submit'), $this->validPayload());
        $unsignedPath = (string) parse_url($storeResponse->json('receipt.excel_url'), PHP_URL_PATH);

        $this->get($unsignedPath)->assertForbidden();
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
            'report_url' => '',
        ];
    }
}
