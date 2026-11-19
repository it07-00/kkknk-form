<?php

namespace App\Filament\Widgets;

use App\Models\GhgSubmission;
use Filament\Widgets\ChartWidget;

class GhgEmissionsByYearChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Tổng phát thải Phạm vi 1 & 2 theo năm (tCO₂e)';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $years = array_map(
            static fn (int|string $year): string => (string) $year,
            config('ghg.reporting_years', ['2024', '2025', '2026'])
        );

        $scope1ByYear = array_fill_keys($years, 0.0);
        $scope2ByYear = array_fill_keys($years, 0.0);

        foreach (GhgSubmission::query()->get(['data']) as $submission) {
            foreach ($years as $year) {
                $yearData = data_get($submission->data, "inventory.{$year}");

                if (! is_array($yearData)) {
                    continue;
                }

                $scope1ByYear[$year] += (float) ($yearData['scope1_emissions'] ?? 0);
                $scope2ByYear[$year] += (float) ($yearData['scope2_emissions'] ?? 0);
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Phạm vi 1 (tCO₂e)',
                    'data' => array_values($scope1ByYear),
                    'backgroundColor' => '#003c33',
                ],
                [
                    'label' => 'Phạm vi 2 (tCO₂e)',
                    'data' => array_values($scope2ByYear),
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => array_map(static fn (string $year): string => "Năm {$year}", $years),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
