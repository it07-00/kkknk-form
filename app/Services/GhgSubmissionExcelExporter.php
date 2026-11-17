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

            if ($archive->open($outputPath) !== true) {
                throw new RuntimeException('Không thể mở lại file Excel để đồng bộ shared strings.');
            }

            $isOpen = true;
            $this->syncSharedStringsCount($archive);

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
        $rows = [];
        $yearRanges = [];
        $sequence = 1;

        foreach ($years as $year) {
            $inventory = Arr::get($data, "inventory.{$year}", []);
            $boilers = $this->equipmentList($inventory, 'boilers', 'boiler');
            $refrigerationSystems = $this->equipmentList($inventory, 'refrigeration_systems', 'refrigeration');
            $scope1Sources = array_values(array_filter(
                Arr::get($inventory, 'scope1_sources', []),
                is_array(...)
            ));
            $scope1Emissions = $this->numericValue(Arr::get($inventory, 'scope1_emissions'));
            $scope2Emissions = $this->numericValue(Arr::get($inventory, 'scope2_emissions'));
            $details = [
                ...array_map(fn (array $boiler): array => ['type' => 'boiler', 'data' => $boiler], $boilers),
                ...array_map(fn (array $system): array => ['type' => 'refrigeration', 'data' => $system], $refrigerationSystems),
                ...array_map(fn (array $source): array => ['type' => 'scope1', 'data' => $source], $scope1Sources),
            ];

            if ($details === []) {
                $details[] = ['type' => 'summary', 'data' => []];
            }

            $startRow = 6 + count($rows);

            foreach ($details as $detailOffset => $detail) {
                $isSummaryRow = $detailOffset === 0;
                $isFirstSubmissionRow = $rows === [];
                $detailData = $detail['data'];
                $type = $detail['type'];
                $boiler = $type === 'boiler' ? $detailData : [];
                $refrigeration = $type === 'refrigeration' ? $detailData : [];
                $source = $type === 'scope1' ? $detailData : [];

                $rows[] = [
                    'A' => $isSummaryRow ? $sequence : null,
                    'B' => $isFirstSubmissionRow ? Arr::get($company, 'name') : null,
                    'C' => $isSummaryRow ? "Năm {$year}" : null,
                    'D' => $isFirstSubmissionRow ? Arr::get($company, 'address') : null,
                    'E' => $isFirstSubmissionRow ? Arr::get($company, 'industry') : null,
                    'F' => $isFirstSubmissionRow ? (string) Arr::get($company, 'tax_code', '') : null,
                    'G' => $isFirstSubmissionRow ? $this->contactLabel($company) : null,
                    'H' => $this->choiceLabel($source, 'fuel_type'),
                    'I' => $isSummaryRow ? $this->numericValue(Arr::get($inventory, 'grid_electricity_kwh')) : null,
                    'J' => $isSummaryRow ? $this->numericValue(Arr::get($inventory, 'solar_electricity_kwh')) : null,
                    'K' => Arr::get($boiler, 'capacity'),
                    'L' => $this->choiceLabel($boiler, 'fuel'),
                    'M' => $this->measurement(Arr::get($boiler, 'consumption'), $this->choiceLabel($boiler, 'unit')),
                    'N' => $this->choiceLabel($refrigeration, 'equipment'),
                    'O' => Arr::get($refrigeration, 'capacity'),
                    'P' => $this->choiceLabel($refrigeration, 'gas_type'),
                    'Q' => $this->numericValue(Arr::get($refrigeration, 'full_charge_kg')),
                    'R' => $isSummaryRow ? $this->numericValue(Arr::get($inventory, 'energy_toe')) : null,
                    'S' => $this->detailSourceType($type, $source),
                    'T' => $this->detailFuel($type, $boiler, $refrigeration, $source),
                    'U' => $this->detailQuantity($type, $boiler, $refrigeration, $source),
                    'V' => $this->detailUnit($type, $boiler, $source),
                    'W' => $this->detailNote($type, $refrigeration, $source),
                    'X' => $isSummaryRow ? $scope1Emissions : null,
                    'Y' => $isSummaryRow ? $scope2Emissions : null,
                    'Z' => $isSummaryRow && $scope1Emissions !== null && $scope2Emissions !== null
                        ? $scope1Emissions + $scope2Emissions
                        : null,
                    'AA' => $this->numericValue(Arr::get($refrigeration, 'recharge_kg')),
                    'AB' => $isSummaryRow ? Arr::get($inventory, 'report_method') : null,
                    'AC' => $isSummaryRow ? Arr::get($inventory, 'report_url') : null,
                ];
            }

            $yearRanges[] = [$startRow, 5 + count($rows)];
            $sequence++;
        }

        $this->writeInventoryRows($archive, $rows, $yearRanges);
    }

    /**
     * @param  array<int, array<string, int|float|string|null>>  $rows
     * @param  array<int, array{0: int, 1: int}>  $yearRanges
     */
    private function writeInventoryRows(ZipArchive $archive, array $rows, array $yearRanges): void
    {
        $entryName = 'xl/worksheets/sheet1.xml';
        $document = $this->worksheetDocument($archive, $entryName);
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('x', self::SPREADSHEET_NAMESPACE);

        foreach ([
            'S4' => 'Chi tiết nguồn phát thải',
            'S5' => 'Loại nguồn',
            'T5' => 'Nhiên liệu / môi chất',
            'U5' => 'Số lượng',
            'V5' => 'Đơn vị',
            'W5' => 'Ghi chú',
        ] as $reference => $value) {
            $cell = $xpath->query("//x:c[@r='{$reference}']")?->item(0);

            if (! $cell instanceof DOMElement) {
                throw new RuntimeException("Không tìm thấy ô {$reference} trong file Excel mẫu.");
            }

            $this->replaceCellValue($document, $cell, $value);
        }

        foreach (['T4', 'U4', 'V4', 'W4'] as $reference) {
            $cell = $xpath->query("//x:c[@r='{$reference}']")?->item(0);

            if ($cell instanceof DOMElement) {
                $this->replaceCellValue($document, $cell, null);
            }
        }

        $this->appendHeaderCells($document, $xpath);

        $sheetData = $xpath->query('//x:sheetData')?->item(0);

        if (! $sheetData instanceof DOMElement) {
            throw new RuntimeException('Không tìm thấy dữ liệu bảng trong file Excel mẫu.');
        }

        foreach (iterator_to_array($xpath->query('//x:sheetData/x:row') ?: []) as $existingRow) {
            if ($existingRow instanceof DOMElement && (int) $existingRow->getAttribute('r') >= 6) {
                $sheetData->removeChild($existingRow);
            }
        }

        foreach ($rows as $offset => $values) {
            $rowNumber = 6 + $offset;
            $row = $document->createElementNS(self::SPREADSHEET_NAMESPACE, 'row');
            $row->setAttribute('r', (string) $rowNumber);
            $row->setAttribute('spans', '1:29');
            $row->setAttribute('ht', '45');
            $row->setAttribute('customHeight', '1');

            foreach ($this->inventoryColumns() as $column => $style) {
                $row->appendChild($this->createCell(
                    $document,
                    $column.$rowNumber,
                    $style,
                    $values[$column] ?? null
                ));
            }

            $sheetData->appendChild($row);
        }

        $lastRow = max(5, 5 + count($rows));
        $dimension = $xpath->query('//x:dimension')?->item(0);

        if ($dimension instanceof DOMElement) {
            $dimension->setAttribute('ref', "A4:AC{$lastRow}");
        }

        $this->extendInventoryColumns($document, $xpath);
        $this->updateInventoryMerges($document, $xpath, $yearRanges);
        $this->saveWorksheetDocument($archive, $entryName, $document);
    }

    private function appendHeaderCells(DOMDocument $document, DOMXPath $xpath): void
    {
        $headers = [
            'X4' => 'Tổng phát thải theo năm (tCO₂e)',
            'X5' => 'Phạm vi 1',
            'Y5' => 'Phạm vi 2',
            'Z5' => 'Tổng cộng',
            'AA4' => 'Thông tin bổ sung',
            'AA5' => 'Lượng nạp bổ sung (kg)',
            'AB5' => 'Phương thức báo cáo',
            'AC5' => 'Liên kết báo cáo',
        ];

        foreach ($headers as $reference => $value) {
            $rowNumber = (int) preg_replace('/\D/', '', $reference);
            $row = $xpath->query("//x:row[@r='{$rowNumber}']")?->item(0);

            if (! $row instanceof DOMElement) {
                throw new RuntimeException("Không tìm thấy hàng {$rowNumber} trong file Excel mẫu.");
            }

            $row->appendChild($this->createCell(
                $document,
                $reference,
                $rowNumber === 4 ? '5' : '4',
                $value
            ));
            $row->setAttribute('spans', '1:29');
        }

        $rowFour = $xpath->query("//x:row[@r='4']")?->item(0);

        if ($rowFour instanceof DOMElement) {
            foreach (['Y4', 'Z4', 'AB4', 'AC4'] as $reference) {
                $rowFour->appendChild($this->createCell($document, $reference, '6', null));
            }
        }
    }

    /**
     * @return array<string, string>
     */
    private function inventoryColumns(): array
    {
        return [
            'A' => '2', 'B' => '2', 'C' => '2', 'D' => '2', 'E' => '2', 'F' => '2', 'G' => '2', 'H' => '2',
            'I' => '12', 'J' => '12', 'K' => '2', 'L' => '2', 'M' => '2', 'N' => '2', 'O' => '2', 'P' => '2',
            'Q' => '12', 'R' => '12', 'S' => '2', 'T' => '2', 'U' => '12', 'V' => '2', 'W' => '2', 'X' => '12',
            'Y' => '12', 'Z' => '12', 'AA' => '12', 'AB' => '2', 'AC' => '2',
        ];
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
        $document = $this->worksheetDocument($archive, $entryName);

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('x', self::SPREADSHEET_NAMESPACE);

        foreach ($cells as $reference => $value) {
            $cell = $xpath->query("//x:c[@r='{$reference}']")?->item(0);

            if (! $cell instanceof DOMElement) {
                throw new RuntimeException("Không tìm thấy ô {$reference} trong file Excel mẫu.");
            }

            $this->replaceCellValue($document, $cell, $value);
        }

        $this->saveWorksheetDocument($archive, $entryName, $document);
    }

    private function worksheetDocument(ZipArchive $archive, string $entryName): DOMDocument
    {
        $xml = $archive->getFromName($entryName);

        if ($xml === false) {
            throw new RuntimeException("Thiếu thành phần {$entryName} trong file Excel mẫu.");
        }

        $document = new DOMDocument('1.0', 'UTF-8');

        if (! $document->loadXML($xml, LIBXML_NONET)) {
            throw new RuntimeException("Không thể đọc thành phần {$entryName} trong file Excel mẫu.");
        }

        return $document;
    }

    private function saveWorksheetDocument(
        ZipArchive $archive,
        string $entryName,
        DOMDocument $document
    ): void {
        $updatedXml = $document->saveXML();

        if ($updatedXml === false || ! $archive->addFromString($entryName, $updatedXml)) {
            throw new RuntimeException("Không thể cập nhật thành phần {$entryName} trong file Excel.");
        }
    }

    /**
     * Synchronize the count and uniqueCount attributes of sharedStrings.xml
     * with the actual shared-string references across all worksheets.
     */
    private function syncSharedStringsCount(ZipArchive $archive): void
    {
        $entryName = 'xl/sharedStrings.xml';
        $xml = $archive->getFromName($entryName);

        if ($xml === false) {
            return;
        }

        $totalReferences = 0;

        foreach (['xl/worksheets/sheet1.xml', 'xl/worksheets/sheet2.xml'] as $sheetEntry) {
            $sheetXml = $archive->getFromName($sheetEntry);

            if ($sheetXml === false) {
                continue;
            }

            $sheetDocument = new DOMDocument('1.0', 'UTF-8');
            $sheetDocument->loadXML($sheetXml, LIBXML_NONET);
            $sheetXpath = new DOMXPath($sheetDocument);
            $sheetXpath->registerNamespace('x', self::SPREADSHEET_NAMESPACE);

            $totalReferences += $sheetXpath->query('//x:c[@t="s"]')?->length ?? 0;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadXML($xml, LIBXML_NONET);
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('x', self::SPREADSHEET_NAMESPACE);

        $sst = $xpath->query('//x:sst')?->item(0);

        if (! $sst instanceof DOMElement) {
            return;
        }

        $uniqueCount = $xpath->query('//x:sst/x:si')?->length ?? 0;

        $sst->setAttribute('count', (string) $totalReferences);
        $sst->setAttribute('uniqueCount', (string) $uniqueCount);

        $updatedXml = $document->saveXML();

        if ($updatedXml !== false) {
            $archive->addFromString($entryName, $updatedXml);
        }
    }

    private function createCell(
        DOMDocument $document,
        string $reference,
        string $style,
        int|float|string|null $value
    ): DOMElement {
        $cell = $document->createElementNS(self::SPREADSHEET_NAMESPACE, 'c');
        $cell->setAttribute('r', $reference);
        $cell->setAttribute('s', $style);
        $this->replaceCellValue($document, $cell, $value);

        return $cell;
    }

    private function extendInventoryColumns(DOMDocument $document, DOMXPath $xpath): void
    {
        $columns = $xpath->query('//x:cols')?->item(0);
        $trailingColumn = $xpath->query("//x:cols/x:col[@min='24'][@max='16384']")?->item(0);

        if (! $columns instanceof DOMElement || ! $trailingColumn instanceof DOMElement) {
            throw new RuntimeException('Không tìm thấy cấu hình cột trong file Excel mẫu.');
        }

        foreach ([19 => 24, 20 => 20, 21 => 14, 22 => 14, 23 => 32] as $index => $width) {
            $column = $xpath->query("//x:cols/x:col[@min='{$index}'][@max='{$index}']")?->item(0);

            if ($column instanceof DOMElement) {
                $column->setAttribute('width', (string) $width);
            }
        }

        $trailingColumn->setAttribute('min', '30');

        foreach ([24 => 14, 25 => 14, 26 => 14, 27 => 20, 28 => 28, 29 => 36] as $index => $width) {
            $column = $document->createElementNS(self::SPREADSHEET_NAMESPACE, 'col');
            $column->setAttribute('min', (string) $index);
            $column->setAttribute('max', (string) $index);
            $column->setAttribute('width', (string) $width);
            $column->setAttribute('style', '1');
            $column->setAttribute('customWidth', '1');
            $columns->insertBefore($column, $trailingColumn);
        }
    }

    /**
     * @param  array<int, array{0: int, 1: int}>  $yearRanges
     */
    private function updateInventoryMerges(
        DOMDocument $document,
        DOMXPath $xpath,
        array $yearRanges
    ): void {
        $mergeCells = $xpath->query('//x:mergeCells')?->item(0);

        if (! $mergeCells instanceof DOMElement) {
            throw new RuntimeException('Không tìm thấy vùng gộp ô trong file Excel mẫu.');
        }

        $obsoleteMerges = ['B6:B8', 'S4:T4', 'U4:W4'];

        foreach (iterator_to_array($xpath->query('//x:mergeCells/x:mergeCell') ?: []) as $mergeCell) {
            if ($mergeCell instanceof DOMElement && in_array($mergeCell->getAttribute('ref'), $obsoleteMerges, true)) {
                $mergeCells->removeChild($mergeCell);
            }
        }

        foreach (['S4:W4', 'X4:Z4', 'AA4:AC4'] as $reference) {
            $this->appendMerge($document, $mergeCells, $reference);
        }

        if ($yearRanges !== []) {
            $submissionStartRow = $yearRanges[0][0];
            $submissionEndRow = $yearRanges[array_key_last($yearRanges)][1];

            if ($submissionStartRow !== $submissionEndRow) {
                foreach (['B', 'D', 'E', 'F', 'G'] as $column) {
                    $this->appendMerge(
                        $document,
                        $mergeCells,
                        "{$column}{$submissionStartRow}:{$column}{$submissionEndRow}"
                    );
                }
            }
        }

        foreach ($yearRanges as [$startRow, $endRow]) {
            if ($startRow === $endRow) {
                continue;
            }

            foreach (['A', 'C', 'I', 'J', 'R', 'X', 'Y', 'Z', 'AB', 'AC'] as $column) {
                $this->appendMerge($document, $mergeCells, "{$column}{$startRow}:{$column}{$endRow}");
            }
        }

        $mergeCells->setAttribute('count', (string) $mergeCells->childElementCount);
    }

    private function appendMerge(
        DOMDocument $document,
        DOMElement $mergeCells,
        string $reference
    ): void {
        $mergeCell = $document->createElementNS(self::SPREADSHEET_NAMESPACE, 'mergeCell');
        $mergeCell->setAttribute('ref', $reference);
        $mergeCells->appendChild($mergeCell);
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
     * @param  array<string, mixed>  $source
     */
    private function detailSourceType(string $type, array $source): ?string
    {
        return match ($type) {
            'boiler' => 'Lò hơi',
            'refrigeration' => 'Môi chất lạnh',
            'scope1' => $this->choiceLabel($source, 'source_type'),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $boiler
     * @param  array<string, mixed>  $refrigeration
     * @param  array<string, mixed>  $source
     */
    private function detailFuel(
        string $type,
        array $boiler,
        array $refrigeration,
        array $source
    ): ?string {
        return match ($type) {
            'boiler' => $this->choiceLabel($boiler, 'fuel'),
            'refrigeration' => $this->choiceLabel($refrigeration, 'gas_type'),
            'scope1' => $this->choiceLabel($source, 'fuel_type'),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $boiler
     * @param  array<string, mixed>  $refrigeration
     * @param  array<string, mixed>  $source
     */
    private function detailQuantity(
        string $type,
        array $boiler,
        array $refrigeration,
        array $source
    ): ?float {
        return match ($type) {
            'boiler' => $this->numericValue(Arr::get($boiler, 'consumption')),
            'refrigeration' => $this->numericValue(Arr::get($refrigeration, 'full_charge_kg')),
            'scope1' => $this->numericValue(Arr::get($source, 'quantity')),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $boiler
     * @param  array<string, mixed>  $source
     */
    private function detailUnit(string $type, array $boiler, array $source): ?string
    {
        return match ($type) {
            'boiler' => $this->choiceLabel($boiler, 'unit'),
            'refrigeration' => 'kg',
            'scope1' => $this->choiceLabel($source, 'unit'),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $refrigeration
     * @param  array<string, mixed>  $source
     */
    private function detailNote(string $type, array $refrigeration, array $source): ?string
    {
        if ($type === 'scope1') {
            $note = Arr::get($source, 'note');

            return is_string($note) && trim($note) !== '' ? trim($note) : null;
        }

        if ($type !== 'refrigeration') {
            return null;
        }

        $parts = array_filter([
            $this->choiceLabel($refrigeration, 'equipment'),
            Arr::get($refrigeration, 'capacity'),
        ], fn (mixed $value): bool => is_string($value) && trim($value) !== '');

        return $parts === [] ? null : implode(' — ', $parts);
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
