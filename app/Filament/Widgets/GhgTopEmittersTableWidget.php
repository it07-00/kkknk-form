<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\GhgSubmissions\GhgSubmissionResource;
use App\Models\GhgSubmission;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class GhgTopEmittersTableWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Hồ sơ tiếp nhận gần đây & Tổng phát thải';

    public function table(Table $table): Table
    {
        return $table
            ->query(GhgSubmission::query()->latest('created_at')->limit(8))
            ->columns([
                TextColumn::make('code')
                    ->label('Mã hồ sơ')
                    ->searchable(),
                TextColumn::make('company_name')
                    ->label('Doanh nghiệp')
                    ->limit(38),
                TextColumn::make('tax_id')
                    ->label('Mã số thuế'),
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
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Tiếp nhận lúc')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Xem chi tiết')
                    ->icon('heroicon-o-eye')
                    ->url(fn (GhgSubmission $record): string => GhgSubmissionResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
