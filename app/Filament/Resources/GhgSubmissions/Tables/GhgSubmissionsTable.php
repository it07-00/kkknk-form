<?php

namespace App\Filament\Resources\GhgSubmissions\Tables;

use App\GhgSubmissionStatus;
use App\Models\GhgSubmission;
use App\Services\GhgSubmissionExcelExporter;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GhgSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Mã hồ sơ')
                    ->searchable(),
                TextColumn::make('company_name')
                    ->label('Doanh nghiệp')
                    ->searchable(),
                TextColumn::make('tax_id')
                    ->label('Mã số thuế')
                    ->searchable(),
                TextColumn::make('reporting_years')
                    ->label('Kỳ kiểm kê')
                    ->badge(),
                TextColumn::make('total_emissions')
                    ->label('Tổng phát thải (tCO₂e)')
                    ->state(function (GhgSubmission $record): float {
                        $inventories = data_get($record->data, 'inventory', []);

                        return collect($inventories)->sum(
                            fn (array $item): float => (float) ($item['scope1_emissions'] ?? 0) + (float) ($item['scope2_emissions'] ?? 0)
                        );
                    })
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),
                IconColumn::make('data.mitigation.implemented')
                    ->label('Giảm nhẹ')
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tiếp nhận lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(GhgSubmissionStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('downloadExcel')
                    ->label('Tải Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (): bool => auth()->user()?->hasPermissionOrLegacyAdmin('submissions.export') ?? false)
                    ->url(fn (GhgSubmission $record): string => route('admin.ghg-submissions.excel.download', $record)),
                Action::make('downloadReport')
                    ->label('Tải báo cáo đã nộp')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn (GhgSubmission $record): bool => $record->mitigation_report_path !== null
                        && (auth()->user()?->hasPermissionOrLegacyAdmin('submissions.download_report') ?? false))
                    ->url(fn (GhgSubmission $record): string => route('admin.ghg-submissions.report.download', $record)),
            ])
            ->toolbarActions([
                BulkAction::make('exportSelected')
                    ->label('Xuất Excel tổng hợp')
                    ->icon('heroicon-o-table-cells')
                    ->visible(fn (): bool => auth()->user()?->hasPermissionOrLegacyAdmin('submissions.export') ?? false)
                    ->action(function (Collection $records): BinaryFileResponse {
                        $exporter = app(GhgSubmissionExcelExporter::class);
                        $path = $exporter->exportMany($records);

                        return response()->download(
                            $path,
                            $exporter->downloadManyName(),
                            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                        )->deleteFileAfterSend();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
