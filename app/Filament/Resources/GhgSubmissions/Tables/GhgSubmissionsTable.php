<?php

namespace App\Filament\Resources\GhgSubmissions\Tables;

use App\GhgSubmissionStatus;
use App\Models\GhgSubmission;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                    ->url(fn (GhgSubmission $record): string => route('admin.ghg-submissions.excel.download', $record)),
                Action::make('downloadReport')
                    ->label('Tải báo cáo đã nộp')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn (GhgSubmission $record): bool => $record->mitigation_report_path !== null)
                    ->url(fn (GhgSubmission $record): string => route('admin.ghg-submissions.report.download', $record)),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
