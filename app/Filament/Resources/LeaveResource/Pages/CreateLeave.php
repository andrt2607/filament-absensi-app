<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;

    //add default value on create form
    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';
        return $data;
    }
}
