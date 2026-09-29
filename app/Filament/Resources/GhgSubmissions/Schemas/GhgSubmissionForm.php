<?php

namespace App\Filament\Resources\GhgSubmissions\Schemas;

use App\GhgSubmissionStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GhgSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('company_name'),
                TextInput::make('tax_id'),
                Textarea::make('reporting_years')
                    ->columnSpanFull(),
                Textarea::make('data')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(GhgSubmissionStatus::class)
                    ->default('submitted')
                    ->required(),
                TextInput::make('ip_address'),
                TextInput::make('mitigation_report_path'),
                TextInput::make('mitigation_report_original_name'),
                TextInput::make('mitigation_report_mime_type'),
                TextInput::make('mitigation_report_size')
                    ->numeric(),
                TextInput::make('reviewed_by')
                    ->numeric(),
                DateTimePicker::make('reviewed_at'),
            ]);
    }
}
