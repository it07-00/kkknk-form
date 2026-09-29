<?php

namespace App\Filament\Resources\GhgSubmissions\Pages;

use App\Filament\Resources\GhgSubmissions\GhgSubmissionResource;
use App\GhgSubmissionStatus;
use App\Models\GhgSubmission;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ViewGhgSubmission extends ViewRecord
{
    protected static string $resource = GhgSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateStatus')
                ->label('Cập nhật trạng thái')
                ->icon('heroicon-o-pencil-square')
                ->fillForm(fn (GhgSubmission $record): array => [
                    'status' => $record->status->value,
                ])
                ->schema([
                    Select::make('status')
                        ->label('Trạng thái')
                        ->options(GhgSubmissionStatus::class)
                        ->rules([Rule::enum(GhgSubmissionStatus::class)])
                        ->required(),
                ])
                ->action(function (array $data, GhgSubmission $record): void {
                    Gate::authorize('update', $record);

                    $status = $data['status'] instanceof GhgSubmissionStatus
                        ? $data['status']
                        : GhgSubmissionStatus::from($data['status']);

                    $record->update([
                        'status' => $status,
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);
                })
                ->successNotificationTitle('Đã cập nhật trạng thái hồ sơ'),
            Action::make('downloadExcel')
                ->label('Tải Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (GhgSubmission $record): string => route('admin.ghg-submissions.excel.download', $record)),
            Action::make('downloadReport')
                ->label('Tải báo cáo đã nộp')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn (GhgSubmission $record): bool => $record->mitigation_report_path !== null)
                ->url(fn (GhgSubmission $record): string => route('admin.ghg-submissions.report.download', $record)),
        ];
    }
}
