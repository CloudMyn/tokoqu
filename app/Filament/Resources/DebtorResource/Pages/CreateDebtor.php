<?php

namespace App\Filament\Resources\DebtorResource\Pages;

use App\Filament\Resources\DebtorResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDebtor extends CreateRecord
{
    protected static string $resource = DebtorResource::class;

    protected static ?string $title = 'Input Data Debitur';


}
