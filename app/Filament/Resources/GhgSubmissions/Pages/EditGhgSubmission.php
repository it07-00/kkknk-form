<?php

namespace App\Filament\Resources\GhgSubmissions\Pages;

use App\Filament\Resources\GhgSubmissions\GhgSubmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGhgSubmission extends EditRecord
{
    protected static string $resource = GhgSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
