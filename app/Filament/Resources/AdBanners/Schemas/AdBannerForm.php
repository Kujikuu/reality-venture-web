<?php

namespace App\Filament\Resources\AdBanners\Schemas;

use App\Enums\BannerPosition;
use App\Models\AdBanner;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AdBannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Banner Details')
                    ->description('Upload the banner image and configure its content.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('e.g. Summer Accelerator Program'),
                        FileUpload::make('image_path')
                            ->label('Banner Image')
                            ->image()
                            ->disk('public')
                            ->directory('banners')
                            ->imageEditor()
                            ->required()
                            ->columnSpanFull()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->helperText('Recommended size: 1440x480px. Max 5MB.'),
                        TextInput::make('link_url')
                            ->label('Link URL')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://example.com')
                            ->prefixIcon('heroicon-o-link'),
                        TextInput::make('alt_text')
                            ->label('Alt Text')
                            ->maxLength(255)
                            ->placeholder('Descriptive text for accessibility')
                            ->prefixIcon('heroicon-o-eye'),
                        Textarea::make('description')
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->rows(3)
                            ->placeholder('Internal notes about this banner...'),
                    ]),
                Section::make('Placement & Scheduling')
                    ->description('Control where and when this banner appears.')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->columns(2)
                    ->schema([
                        Select::make('position')
                            ->options(
                                collect(BannerPosition::cases())
                                    ->mapWithKeys(fn ($p) => [$p->value => $p->label()])
                            )
                            ->required()
                            ->native(false)
                            ->helperText('Where on the homepage this banner will appear'),
                        TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower numbers display first')
                            ->prefixIcon('heroicon-o-arrows-up-down'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Inactive banners won\'t appear on the site'),
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('starts_at')
                                    ->label('Start Date')
                                    ->nullable()
                                    ->prefixIcon('heroicon-o-play'),
                                DateTimePicker::make('ends_at')
                                    ->label('End Date')
                                    ->nullable()
                                    ->after('starts_at')
                                    ->prefixIcon('heroicon-o-stop'),
                            ])
                            ->columnSpanFull(),
                    ]),
                Section::make('Performance')
                    ->description('Track how this banner performs.')
                    ->icon(Heroicon::OutlinedChartBar)
                    ->collapsible()
                    ->schema([
                        Placeholder::make('click_count')
                            ->label('Total Clicks')
                            ->content(fn (?AdBanner $record): string => $record?->click_count ? number_format($record->click_count) : '0'),
                    ])
                    ->hiddenOn('create'),
            ]);
    }
}
