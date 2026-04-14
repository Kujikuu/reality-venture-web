<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\Actions\ApplicationActions;
use App\Filament\Resources\Applications\ApplicationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ApplicationActions::advanceToStartup(),
            ApplicationActions::scheduleInterview(),
            ApplicationActions::evaluate(),
            ApplicationActions::moveToDecision(),
            ApplicationActions::sendAgreement(),
            ApplicationActions::approveAgreement(),
            ApplicationActions::sendDemoDayInvite(),
            ApplicationActions::moveToInvestors(),
            ApplicationActions::changeStatus(),
        ];
    }
}
