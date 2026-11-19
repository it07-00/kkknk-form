<?php

namespace App\Filament\Widgets;

use App\GhgSubmissionStatus;
use App\Models\GhgSubmission;
use Filament\Widgets\ChartWidget;

class GhgSubmissionsByStatusChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Phân bổ hồ sơ theo trạng thái';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $counts = GhgSubmission::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [];
        $values = [];
        $colors = [];

        $palette = [
            GhgSubmissionStatus::Submitted->value => '#3b82f6',
            GhgSubmissionStatus::Reviewing->value => '#f59e0b',
            GhgSubmissionStatus::NeedsRevision->value => '#ef4444',
            GhgSubmissionStatus::Completed->value => '#10b981',
        ];

        foreach (GhgSubmissionStatus::cases() as $status) {
            $labels[] = $status->label();
            $values[] = (int) ($counts[$status->value] ?? 0);
            $colors[] = $palette[$status->value];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Số hồ sơ',
                    'data' => $values,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
