<?php

namespace App\Filament\Resources\GhgSubmissions\Pages;

use App\Filament\Resources\GhgSubmissions\GhgSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListGhgSubmissions extends ListRecords
{
    protected static string $resource = GhgSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
