<?php

namespace App\Filament\Resources\GateEntryResource\Pages;

use App\Filament\Resources\GateEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGateEntry extends EditRecord
{
    protected static string $resource = GateEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
