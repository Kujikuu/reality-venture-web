<?php

namespace App\Filament\Resources\Applications\Schemas;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\BusinessStage;
use App\Enums\DiscoverySource;
use App\Enums\FundingRound;
use App\Enums\Industry;
use App\Enums\InterviewType;
use App\Models\Application;
use App\Services\ApplicationWorkflowService;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pipeline')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->schema([
                        TextEntry::make('pipeline_progress')
                            ->label('Progress')
                            ->state(function (Application $record): string {
                                $stages = ApplicationType::ordered();
                                $labels = array_map(
                                    fn (ApplicationType $stage): string => $stage === $record->type
                                        ? '▶ '.$stage->label()
                                        : ($stage->order() < $record->type->order() ? '✓ '.$stage->label() : '○ '.$stage->label()),
                                    $stages,
                                );

                                return implode(' → ', $labels);
                            })
                            ->columnSpanFull(),
                        TextEntry::make('recommended_action')
                            ->label('Recommended Next Action')
                            ->state(fn (Application $record): string => app(ApplicationWorkflowService::class)->recommendedAction($record) ?? 'No further action required')
                            ->columnSpanFull(),
                    ]),

                Section::make('Applicant Information')
                    ->icon(Heroicon::OutlinedUser)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('type')
                            ->label('Stage')
                            ->badge()
                            ->formatStateUsing(fn (ApplicationType $state): string => $state->label())
                            ->color(fn (ApplicationType $state): string => $state->color()),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (ApplicationStatus $state): string => $state->label())
                            ->color(fn (ApplicationStatus $state): string => $state->color()),
                        TextEntry::make('uid')
                            ->name('Reference'),
                        TextEntry::make('first_name')
                            ->label('First Name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('last_name')
                            ->label('Last Name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('email')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage('Email copied'),
                        TextEntry::make('phone')
                            ->icon('heroicon-o-phone')
                            ->copyable()
                            ->copyMessage('Phone copied')
                            ->placeholder('Not provided'),
                        TextEntry::make('social_profile')
                            ->label('Social Profile')
                            ->icon('heroicon-o-link')
                            ->url(fn (Application $record): ?string => $record->social_profile)
                            ->openUrlInNewTab()
                            ->placeholder('Not provided')
                            ->color('primary'),
                        TextEntry::make('city')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('Not provided'),
                    ]),

                Section::make('General Inquiry')
                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->visible(fn (Application $record): bool => $record->type === ApplicationType::Initial)
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->prose(),
                    ]),

                Section::make('Company Details')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->columns(2)
                    ->visible(fn (Application $record): bool => filled($record->company_name))
                    ->schema([
                        TextEntry::make('business_stage')
                            ->label('Business Stage')
                            ->badge()
                            ->formatStateUsing(fn (?BusinessStage $state): string => $state?->label() ?? '—'),
                        TextEntry::make('company_name')
                            ->label('Company Name'),
                        TextEntry::make('number_of_founders')
                            ->label('Number of Founders'),
                        TextEntry::make('hq_country')
                            ->label('HQ Country'),
                        TextEntry::make('website_link')
                            ->label('Website')
                            ->url(fn (Application $record): ?string => $record->website_link)
                            ->openUrlInNewTab()
                            ->color('primary'),
                        TextEntry::make('founded_date')
                            ->label('Founded')
                            ->date('M Y'),
                        TextEntry::make('industry')
                            ->label('Industry')
                            ->badge()
                            ->formatStateUsing(fn (?Industry $state): string => $state?->label() ?? '—'),
                        TextEntry::make('industry_other')
                            ->label('Industry (Other)')
                            ->visible(fn (Application $record): bool => $record->industry === Industry::Other)
                            ->columnSpanFull(),
                        TextEntry::make('company_description')
                            ->label('Company Description')
                            ->columnSpanFull()
                            ->prose(),
                        TextEntry::make('attachment_path')
                            ->label('Attachment')
                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '—')
                            ->url(fn (Application $record): ?string => $record->attachment_path ? asset('storage/'.$record->attachment_path) : null)
                            ->openUrlInNewTab()
                            ->placeholder('No file uploaded')
                            ->color('primary'),
                    ]),

                Section::make('Investment Details')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columns(2)
                    ->visible(fn (Application $record): bool => filled($record->company_name))
                    ->schema([
                        TextEntry::make('current_funding_round')
                            ->label('Current Funding Round')
                            ->badge()
                            ->formatStateUsing(fn (?FundingRound $state): string => $state?->label() ?? '—'),
                        TextEntry::make('investment_ask_sar')
                            ->label('Investment Ask')
                            ->formatStateUsing(fn (?int $state): string => $state ? number_format($state).' SAR' : '—'),
                        TextEntry::make('valuation_sar')
                            ->label('Valuation')
                            ->formatStateUsing(fn (?int $state): string => $state ? number_format($state).' SAR' : '—'),
                        TextEntry::make('demo_link')
                            ->label('Demo Link')
                            ->url(fn (Application $record): ?string => $record->demo_link)
                            ->openUrlInNewTab()
                            ->placeholder('Not provided')
                            ->color('primary'),
                        TextEntry::make('previous_funding')
                            ->label('Previous Funding & Investors')
                            ->columnSpanFull()
                            ->prose()
                            ->placeholder('None'),
                    ]),

                Section::make('Evaluation & Interview')
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->columns(2)
                    ->visible(fn (Application $record): bool => in_array($record->type, [ApplicationType::Interview, ApplicationType::Evaluation, ApplicationType::Decision, ApplicationType::SignAgreement, ApplicationType::DemoDay, ApplicationType::Investors]))
                    ->schema([
                        TextEntry::make('interview_type')
                            ->label('Interview Type')
                            ->badge()
                            ->formatStateUsing(fn (?InterviewType $state): string => $state?->label() ?? '—'),
                        TextEntry::make('interview_scheduled_at')
                            ->label('Interview Scheduled')
                            ->dateTime('M d, Y H:i')
                            ->placeholder('Not scheduled'),
                        TextEntry::make('interview_url')
                            ->label('Meeting URL')
                            ->url(fn (Application $record): ?string => $record->interview_url)

                            ->openUrlInNewTab()
                            ->visible(fn (Application $record) => $record->interview_type === InterviewType::Online)
                            ->placeholder('—'),
                        TextEntry::make('interview_location')
                            ->label('Location / Address')
                            ->visible(fn (Application $record) => $record->interview_type === InterviewType::InPerson)
                            ->placeholder('—'),
                        TextEntry::make('evaluation_notes')
                            ->label('Evaluation Notes')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('evaluation_checklist')
                            ->label('Checklist')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cr' => 'Commercial Registration / ID',
                                'logo' => 'Professional Logo',
                                'website' => 'Functional Website/App',
                                'deck' => 'Pitch Deck',
                                'model' => 'Business Model',
                                'team' => 'Team Profiles',
                                'financials' => 'Financial Projections',
                                default => $state,
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Demo Day')
                    ->icon(Heroicon::OutlinedPresentationChartBar)
                    ->columns(2)
                    ->visible(fn (Application $record): bool => in_array($record->type, [ApplicationType::DemoDay, ApplicationType::Investors], true))
                    ->schema([
                        TextEntry::make('demo_day_date')
                            ->label('Date & Time')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('demo_day_type')
                            ->label('Meeting Type')
                            ->badge()
                            ->formatStateUsing(fn (?InterviewType $state): string => $state?->label() ?? '—'),
                        TextEntry::make('demo_day_location')
                            ->label(fn (Application $record): string => $record->demo_day_type === InterviewType::Online ? 'Google Meet Link' : 'Location')
                            ->icon('heroicon-o-map-pin')
                            ->url(fn (Application $record): ?string => $record->demo_day_type === InterviewType::Online ? $record->demo_day_location : null)
                            ->openUrlInNewTab()
                            ->color('primary'),
                        TextEntry::make('demo_day_requirements')
                            ->label('Requirements')
                            ->state(fn (Application $record): array => ApplicationWorkflowService::normalizeDemoDayRequirements($record->demo_day_requirements))
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->columnSpanFull(),
                    ]),

                Section::make('Agreement')
                    ->icon(Heroicon::OutlinedDocumentCheck)
                    ->columns(2)
                    ->visible(fn (Application $record): bool => $record->type->order() >= ApplicationType::SignAgreement->order())
                    ->schema([
                        TextEntry::make('agreement_signer_name')
                            ->label('Signer Name')
                            ->placeholder('Not signed yet'),
                        TextEntry::make('agreement_signed_at')
                            ->label('Signed At')
                            ->dateTime('M d, Y H:i')
                            ->placeholder('Not signed yet'),
                        TextEntry::make('agreement_pdf_path')
                            ->label('Signed PDF')
                            ->placeholder('Not generated yet')
                            ->visible(fn (Application $record): bool => filled($record->agreement_pdf_path))
                            ->url(fn (Application $record): string => route('admin.applications.agreement-pdf', $record))
                            ->openUrlInNewTab()
                            ->color('primary')
                            ->formatStateUsing(fn (): string => 'Download signed PDF'),
                        TextEntry::make('agreement_url')
                            ->label('Agreement Page')
                            ->state(fn (Application $record): string => url('/agreement/'.$record->uid))
                            ->url(fn (Application $record): string => url('/agreement/'.$record->uid))
                            ->openUrlInNewTab()
                            ->color('primary')
                            ->columnSpanFull(),
                    ]),

                Section::make('Discovery')
                    ->icon(Heroicon::OutlinedMegaphone)
                    ->columns(2)
                    ->visible(fn (Application $record): bool => filled($record->company_name))
                    ->schema([
                        TextEntry::make('discovery_source')
                            ->label('How They Heard')
                            ->badge()
                            ->formatStateUsing(fn (?DiscoverySource $state): string => $state?->label() ?? '—'),
                        TextEntry::make('referral_name')
                            ->label('Referral Name')
                            ->placeholder('—'),
                        TextEntry::make('referral_param')
                            ->label('Referral Tracking Code')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),

                Section::make('Timeline')
                    ->icon(Heroicon::OutlinedClock)
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Submitted')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-arrow-path'),
                    ]),
            ]);
    }
}
