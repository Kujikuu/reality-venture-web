<?php

namespace App\Filament\Exports;

use App\Models\Application;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ApplicationExporter extends Exporter
{
    protected static ?string $model = Application::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('uid')
                ->label('Reference'),
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('type')
                ->label('Stage')
                ->formatStateUsing(fn ($state) => $state?->label()),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => $state?->label()),
            ExportColumn::make('first_name')
                ->label('First Name'),
            ExportColumn::make('last_name')
                ->label('Last Name'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('phone')
                ->label('Phone'),
            ExportColumn::make('city')
                ->label('City'),
            ExportColumn::make('social_profile')
                ->label('Social Profile'),
            ExportColumn::make('business_stage')
                ->label('Business Stage')
                ->formatStateUsing(fn ($state) => $state?->label()),
            ExportColumn::make('company_name')
                ->label('Company Name'),
            ExportColumn::make('number_of_founders')
                ->label('Founders'),
            ExportColumn::make('hq_country')
                ->label('HQ Country'),
            ExportColumn::make('website_link')
                ->label('Website'),
            ExportColumn::make('founded_date')
                ->label('Founded Date'),
            ExportColumn::make('industry')
                ->label('Industry')
                ->formatStateUsing(fn ($state) => $state?->label()),
            ExportColumn::make('industry_other')
                ->label('Industry (Other)'),
            ExportColumn::make('company_description')
                ->label('Description'),
            ExportColumn::make('current_funding_round')
                ->label('Funding Round')
                ->formatStateUsing(fn ($state) => $state?->label()),
            ExportColumn::make('investment_ask_sar')
                ->label('Investment Ask (SAR)'),
            ExportColumn::make('valuation_sar')
                ->label('Valuation (SAR)'),
            ExportColumn::make('previous_funding')
                ->label('Previous Funding'),
            ExportColumn::make('demo_link')
                ->label('Demo Link'),
            ExportColumn::make('attachment_path')
                ->label('Attachment')
                ->formatStateUsing(fn (?string $state) => $state ? asset('storage/'.$state) : null),
            ExportColumn::make('discovery_source')
                ->label('Discovery Source')
                ->formatStateUsing(fn ($state) => $state?->label()),
            ExportColumn::make('referral_name')
                ->label('Referral Name'),
            ExportColumn::make('referral_param')
                ->label('Referral Code'),
            ExportColumn::make('created_at')
                ->label('Submitted At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your application export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
