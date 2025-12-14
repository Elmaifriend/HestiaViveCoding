<?php

namespace App\Filament\Resources\GateEntryResource\Pages;

use App\Filament\Resources\GateEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGateEntries extends ListRecords
{
    protected static string $resource = GateEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
