<?php

namespace App\Jobs;

use App\Enums\ApplicationType;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Revolution\Google\Sheets\Facades\Sheets;

class SyncApplicationToGoogleSheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public Application $application,
    ) {}

    public function handle(): void
    {
        $spreadsheetId = config('services.google.sheets_spreadsheet_id');

        if (! $spreadsheetId) {
            return;
        }

        $sheet = $this->application->type === ApplicationType::Startup
            ? 'Startup Applications'
            : 'General Applications';

        $row = $this->application->type === ApplicationType::Startup
            ? $this->buildStartupRow()
            : $this->buildGeneralRow();

        Sheets::spreadsheet($spreadsheetId)
            ->sheet($sheet)
            ->append([$row]);
    }

    /** @return array<int, string|null> */
    private function buildStartupRow(): array
    {
        $app = $this->application;

        return [
            $app->created_at?->format('Y-m-d H:i'),
            $app->first_name,
            $app->last_name,
            $app->email,
            $app->phone,
            $app->city,
            $app->linkedin_profile,
            $app->business_stage?->label(),
            $app->company_name,
            (string) $app->number_of_founders,
            $app->hq_country,
            $app->website_link,
            $app->founded_date?->format('Y-m-d'),
            $app->industry?->label(),
            $app->industry_other,
            $app->company_description,
            $app->current_funding_round?->label(),
            $app->investment_ask_sar ? (string) $app->investment_ask_sar : null,
            $app->valuation_sar ? (string) $app->valuation_sar : null,
            $app->previous_funding,
            $app->demo_link,
            $app->attachment_path ? asset('storage/'.$app->attachment_path) : null,
            $app->discovery_source?->label(),
            $app->referral_name,
            $app->referral_param,
        ];
    }

    /** @return array<int, string|null> */
    private function buildGeneralRow(): array
    {
        $app = $this->application;

        return [
            $app->created_at?->format('Y-m-d H:i'),
            $app->first_name,
            $app->last_name,
            $app->email,
            $app->phone,
            $app->city,
            $app->linkedin_profile,
            $app->description,
        ];
    }
}
