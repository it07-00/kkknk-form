<?php

namespace App\Filament\Resources\GhgSubmissions;

use App\Filament\Resources\GhgSubmissions\Pages\ListGhgSubmissions;
use App\Filament\Resources\GhgSubmissions\Pages\ViewGhgSubmission;
use App\Filament\Resources\GhgSubmissions\Schemas\GhgSubmissionInfolist;
use App\Filament\Resources\GhgSubmissions\Tables\GhgSubmissionsTable;
use App\Models\GhgSubmission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GhgSubmissionResource extends Resource
{
    protected static ?string $model = GhgSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'code';

    protected static ?string $modelLabel = 'hồ sơ';

    protected static ?string $pluralModelLabel = 'Hồ sơ kiểm kê';

    protected static ?string $navigationLabel = 'Hồ sơ kiểm kê';

    public static function infolist(Schema $schema): Schema
    {
        return GhgSubmissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GhgSubmissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGhgSubmissions::route('/'),
            'view' => ViewGhgSubmission::route('/{record}'),
        ];
    }
}
