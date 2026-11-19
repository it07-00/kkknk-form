<?php

namespace App\Filament\Widgets;

use App\GhgSubmissionStatus;
use App\Models\GhgSubmission;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GhgStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $submissions = GhgSubmission::query()->get(['status', 'data', 'created_at']);
        $total = $submissions->count();
        $recentCount = $submissions->where('created_at', '>=', now()->subDays(30))->count();

        $pendingCount = $submissions->whereIn('status', [
            GhgSubmissionStatus::Submitted,
            GhgSubmissionStatus::Reviewing,
            GhgSubmissionStatus::NeedsRevision,
        ])->count();
        $completedCount = $submissions->where('status', GhgSubmissionStatus::Completed)->count();
        $completionRate = $total > 0 ? round(($completedCount / $total) * 100, 1) : 0;

        $totalScope1 = 0.0;
        $totalScope2 = 0.0;
        $totalGridKwh = 0.0;
        $totalSolarKwh = 0.0;
        $mitigationImplemented = 0;
        $plannedReduction = 0.0;

        foreach ($submissions as $submission) {
            $data = $submission->data ?? [];

            foreach (data_get($data, 'inventory', []) as $yearData) {
                if (! is_array($yearData)) {
                    continue;
                }

                $totalScope1 += (float) ($yearData['scope1_emissions'] ?? 0);
                $totalScope2 += (float) ($yearData['scope2_emissions'] ?? 0);
                $totalGridKwh += (float) ($yearData['grid_electricity_kwh'] ?? 0);
                $totalSolarKwh += (float) ($yearData['solar_electricity_kwh'] ?? 0);
            }

            if (data_get($data, 'mitigation.implemented') === true) {
                $mitigationImplemented++;
            }

            $plannedReduction += (float) data_get($data, 'mitigation.planned_reduction_tco2e', 0);
        }

        $totalEmissions = $totalScope1 + $totalScope2;
        $mitigationRate = $total > 0 ? round(($mitigationImplemented / $total) * 100, 1) : 0;
        $totalElectricityMwh = ($totalGridKwh + $totalSolarKwh) / 1000;

        return [
            Stat::make('Tổng hồ sơ tiếp nhận', number_format($total, 0, ',', '.'))
                ->description("+{$recentCount} hồ sơ trong 30 ngày qua")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('Chờ xử lý / Hoàn tất', "{$pendingCount} / {$completedCount}")
                ->description("Tỷ lệ hoàn tất: {$completionRate}%")
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color($pendingCount > 0 ? 'warning' : 'success'),
            Stat::make('Tổng phát thải ghi nhận', number_format($totalEmissions, 2, ',', '.').' tCO₂e')
                ->description(sprintf(
                    'Scope 1: %s | Scope 2: %s | Điện: %s MWh',
                    number_format($totalScope1, 1, ',', '.'),
                    number_format($totalScope2, 1, ',', '.'),
                    number_format($totalElectricityMwh, 1, ',', '.')
                ))
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger'),
            Stat::make('Thực hiện giảm nhẹ', "{$mitigationRate}% ({$mitigationImplemented}/{$total})")
                ->description('Mục tiêu cắt giảm: '.number_format($plannedReduction, 1, ',', '.').' tCO₂e')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('success'),
        ];
    }
}
