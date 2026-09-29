<?php

namespace App\Services;

use App\Models\GhgSubmission;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use ZipArchive;

class GhgSubmissionExcelExporter
{
    private const SPREADSHEET_NAMESPACE = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    public function export(GhgSubmission $submission): string
    {
        $templatePath = config('ghg.excel_template');

        if (! is_string($templatePath) || ! is_readable($templatePath)) {
            throw new RuntimeException('Không tìm thấy file Excel mẫu để xuất báo cáo.');
        }

        $outputPath = tempnam(sys_get_temp_dir(), 'ghg-report-');

        if ($outputPath === false || ! copy($templatePath, $outputPath)) {
            throw new RuntimeException('Không thể tạo file Excel tạm thời.');
        }

        $archive = new ZipArchive;
        $isOpen = false;

        try {
            if ($archive->open($outputPath) !== true) {
                throw new RuntimeException('Không thể mở file Excel mẫu.');
            }

            $isOpen = true;
            $this->writeInventorySheet($archive, $submission);
            $this->writeMitigationSheet($archive, $submission);

            if (! $archive->close()) {
                throw new RuntimeException('Không thể hoàn tất file Excel.');
            }

            $isOpen = false;

            return $outputPath;
        } catch (Throwable $exception) {
            if ($isOpen) {
                $archive->close();
            }

            @unlink($outputPath);

            throw $exception;
        }
    }

    public function downloadName(GhgSubmission $submission): string
    {
        $taxCode = Str::slug((string) data_get($submission->data, 'company.tax_code'));

        return sprintf(
            'Bao-cao-KKKNK-%s-%s.xlsx',
            $taxCode !== '' ? $taxCode : 'doanh-nghiep',
            $submission->code
        );
    }

    private function writeInventorySheet(ZipArchive $archive, GhgSubmission $submission): void
    {
        $data = $submission->data;
        $company = Arr::get($data, 'company', []);
        $years = array_values(Arr::get($data, 'reporting_years', []));
        $cells = [];

        for ($offset = 0; $offset < 3; $offset++) {
            $row = 6 + $offset;
            $year = $years[$offset] ?? null;

            if ($year === null) {
                foreach (range('A', 'W') as $column) {
                    $cells["{$column}{$row}"] = null;
                }

                continue;
            }

            $inventory = Arr::get($data, "inventory.{$year}", []);
            $boilers = $this->equipmentList($inventory, 'boilers', 'boiler');
            $refrigerationSystems = $this->equipmentList($inventory, 'refrigeration_systems', 'refrigeration');
            $scope1Emissions = $this->numericValue(Arr::get($inventory, 'scope1_emissions'));
            $scope2Emissions = $this->numericValue(Arr::get($inventory, 'scope2_emissions'));

            $cells += [
                "A{$row}" => $offset + 1,
                "B{$row}" => $offset === 0 ? Arr::get($company, 'name') : null,
                "C{$row}" => "Năm {$year}",
                "D{$row}" => Arr::get($company, 'address'),
                "E{$row}" => Arr::get($company, 'industry'),
                "F{$row}" => (string) Arr::get($company, 'tax_code', ''),
                "G{$row}" => $this->contactLabel($company),
                "H{$row}" => $this->fuelSummary($inventory),
                "I{$row}" => $this->numericValue(Arr::get($inventory, 'grid_electricity_kwh')),
                "J{$row}" => $this->numericValue(Arr::get($inventory, 'solar_electricity_kwh')),
                "K{$row}" => $this->equipmentValues($boilers, fn (array $boiler): mixed => Arr::get($boiler, 'capacity')),
                "L{$row}" => $this->equipmentValues($boilers, fn (array $boiler): ?string => $this->choiceLabel($boiler, 'fuel')),
                "M{$row}" => $this->equipmentValues($boilers, fn (array $boiler): ?string => $this->measurement(Arr::get($boiler, 'consumption'), Arr::get($boiler, 'unit'))),
                "N{$row}" => $this->equipmentValues($refrigerationSystems, fn (array $system): ?string => $this->choiceLabel($system, 'equipment')),
                "O{$row}" => $this->equipmentValues($refrigerationSystems, fn (array $system): mixed => Arr::get($system, 'capacity')),
                "P{$row}" => $this->equipmentValues($refrigerationSystems, fn (array $system): ?string => $this->choiceLabel($system, 'gas_type')),
                "Q{$row}" => $this->equipmentValues($refrigerationSystems, fn (array $system): ?float => $this->numericValue(Arr::get($system, 'full_charge_kg'))),
                "R{$row}" => $this->numericValue(Arr::get($inventory, 'energy_toe')),
                "S{$row}" => $this->scope1Summary($inventory),
                "T{$row}" => $this->scope2Summary($inventory),
                "U{$row}" => $scope1Emissions,
                "V{$row}" => $scope2Emissions,
                "W{$row}" => $scope1Emissions !== null && $scope2Emissions !== null
                    ? $scope1Emissions + $scope2Emissions
                    : null,
            ];
        }

        $this->writeCells($archive, 'xl/worksheets/sheet1.xml', $cells);
    }

    private function writeMitigationSheet(ZipArchive $archive, GhgSubmission $submission): void
    {
        $data = $submission->data;
        $company = Arr::get($data, 'company', []);
        $mitigation = Arr::get($data, 'mitigation', []);
        $planned = $this->numericValue(Arr::get($mitigation, 'planned_reduction_tco2e'));
        $actual = $this->numericValue(Arr::get($mitigation, 'actual_reduction_tco2e'));

        $this->writeCells($archive, 'xl/worksheets/sheet2.xml', [
            'A4' => 1,
            'B4' => Arr::get($company, 'name'),
            'C4' => Arr::get($company, 'address'),
            'D4' => Arr::get($company, 'industry'),
            'E4' => (string) Arr::get($company, 'tax_code', ''),
            'F4' => $this->contactLabel($company),
            'G4' => Arr::get($mitigation, 'plan_2026_2030'),
            'H4' => Arr::get($mitigation, 'implemented_measures'),
            'I4' => $planned,
            'J4' => $actual,
            'K4' => $planned !== null && $planned > 0 && $actual !== null ? $actual / $planned : null,
        ]);
    }

    /**
     * @param  array<string, int|float|string|null>  $cells
     */
    private function writeCells(ZipArchive $archive, string $entryName, array $cells): void
    {
        $xml = $archive->getFromName($entryName);

        if ($xml === false) {
            throw new RuntimeException("Thiếu thành phần {$entryName} trong file Excel mẫu.");
        }

        $document = new DOMDocument('1.0', 'UTF-8');

        if (! $document->loadXML($xml, LIBXML_NONET)) {
            throw new RuntimeException("Không thể đọc thành phần {$entryName} trong file Excel mẫu.");
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('x', self::SPREADSHEET_NAMESPACE);

        foreach ($cells as $reference => $value) {
            $cell = $xpath->query("//x:c[@r='{$reference}']")?->item(0);

            if (! $cell instanceof DOMElement) {
                throw new RuntimeException("Không tìm thấy ô {$reference} trong file Excel mẫu.");
            }

            $this->replaceCellValue($document, $cell, $value);
        }

        $updatedXml = $document->saveXML();

        if ($updatedXml === false || ! $archive->addFromString($entryName, $updatedXml)) {
            throw new RuntimeException("Không thể cập nhật thành phần {$entryName} trong file Excel.");
        }
    }

    private function replaceCellValue(
        DOMDocument $document,
        DOMElement $cell,
        int|float|string|null $value
    ): void {
        while ($cell->firstChild !== null) {
            $cell->removeChild($cell->firstChild);
        }

        $cell->removeAttribute('t');

        if ($value === null || $value === '') {
            return;
        }

        if (is_int($value) || is_float($value)) {
            $cell->appendChild($document->createElementNS(
                self::SPREADSHEET_NAMESPACE,
                'v',
                (string) $value
            ));

            return;
        }

        $cell->setAttribute('t', 'inlineStr');
        $inlineString = $document->createElementNS(self::SPREADSHEET_NAMESPACE, 'is');
        $text = $document->createElementNS(self::SPREADSHEET_NAMESPACE, 't');
        $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
        $text->appendChild($document->createTextNode($value));
        $inlineString->appendChild($text);
        $cell->appendChild($inlineString);
    }

    /**
     * @param  array<string, mixed>  $company
     */
    private function contactLabel(array $company): ?string
    {
        $parts = array_filter([
            Arr::get($company, 'technical_contact.name'),
            Arr::get($company, 'technical_contact.phone'),
        ], fn (mixed $value): bool => is_string($value) && trim($value) !== '');

        return $parts === [] ? null : implode(' ', $parts);
    }

    /**
     * @param  array<string, mixed>  $inventory
     */
    private function fuelSummary(array $inventory): ?string
    {
        $fuels = [];

        if (Arr::get($inventory, 'has_boiler')) {
            foreach ($this->equipmentList($inventory, 'boilers', 'boiler') as $boiler) {
                $fuels[] = $this->choiceLabel($boiler, 'fuel');
            }
        }

        foreach (Arr::get($inventory, 'scope1_sources', []) as $source) {
            if (is_array($source)) {
                $fuels[] = $this->choiceLabel($source, 'fuel_type');
            }
        }

        $fuels = array_values(array_unique(array_filter($fuels)));

        return $fuels === [] ? null : implode('; ', $fuels);
    }

    /**
     * @param  array<string, mixed>  $inventory
     */
    private function scope1Summary(array $inventory): ?string
    {
        $sources = [];

        if (Arr::get($inventory, 'has_boiler')) {
            $sources[] = 'Lò hơi';
        }

        if (Arr::get($inventory, 'has_cooling')) {
            $sources[] = 'Môi chất lạnh';
        }

        foreach (Arr::get($inventory, 'scope1_sources', []) as $source) {
            if (! is_array($source)) {
                continue;
            }

            $label = Arr::get($source, 'note') ?: $this->choiceLabel($source, 'source_type');

            if (is_string($label) && trim($label) !== '') {
                $sources[] = trim($label);
            }
        }

        $sources = array_values(array_unique($sources));

        return $sources === [] ? null : implode('; ', $sources);
    }

    /**
     * @param  array<string, mixed>  $inventory
     * @return array<int, array<string, mixed>>
     */
    private function equipmentList(array $inventory, string $listKey, string $legacyKey): array
    {
        $equipment = Arr::get($inventory, $listKey);

        if (is_array($equipment) && array_is_list($equipment)) {
            return array_values(array_filter($equipment, is_array(...)));
        }

        $legacyEquipment = Arr::get($inventory, $legacyKey);

        return is_array($legacyEquipment) ? [$legacyEquipment] : [];
    }

    /**
     * @param  array<int, array<string, mixed>>  $equipment
     * @param  callable(array<string, mixed>): mixed  $value
     */
    private function equipmentValues(array $equipment, callable $value): int|float|string|null
    {
        $values = array_values(array_filter(
            array_map($value, $equipment),
            fn (mixed $item): bool => $item !== null && $item !== ''
        ));

        if ($values === []) {
            return null;
        }

        if (count($values) === 1) {
            $singleValue = $values[0];

            return is_int($singleValue) || is_float($singleValue) || is_string($singleValue)
                ? $singleValue
                : (string) $singleValue;
        }

        return implode('; ', array_map(
            fn (mixed $item): string => (string) $item,
            $values
        ));
    }

    /**
     * @param  array<string, mixed>  $inventory
     */
    private function scope2Summary(array $inventory): ?string
    {
        $sources = [];

        if (($this->numericValue(Arr::get($inventory, 'grid_electricity_kwh')) ?? 0) > 0) {
            $sources[] = 'Điện lưới';
        }

        if (($this->numericValue(Arr::get($inventory, 'solar_electricity_kwh')) ?? 0) > 0) {
            $sources[] = 'Điện mặt trời';
        }

        return $sources === [] ? null : implode('; ', $sources);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private function choiceLabel(array $values, string $key): ?string
    {
        $value = Arr::get($values, $key);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        if ($value === 'Khác') {
            $other = Arr::get($values, "{$key}_other");

            return is_string($other) && trim($other) !== '' ? trim($other) : null;
        }

        return trim($value);
    }

    private function measurement(mixed $value, mixed $unit): ?string
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        $unitLabel = is_string($unit) ? trim($unit) : '';

        return trim((string) $value.' '.$unitLabel);
    }

    private function numericValue(mixed $value): ?float
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}
