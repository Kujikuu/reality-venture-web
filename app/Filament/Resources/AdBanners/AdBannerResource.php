<?php

namespace App\Filament\Resources\AdBanners;

use App\Filament\Resources\AdBanners\Pages\CreateAdBanner;
use App\Filament\Resources\AdBanners\Pages\EditAdBanner;
use App\Filament\Resources\AdBanners\Pages\ListAdBanners;
use App\Filament\Resources\AdBanners\Schemas\AdBannerForm;
use App\Filament\Resources\AdBanners\Tables\AdBannersTable;
use App\Models\AdBanner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdBannerResource extends Resource
{
    protected static ?string $model = AdBanner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::where('is_active', true)->count();

        return (string) $active;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active banners';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description'];
    }

    public static function form(Schema $schema): Schema
    {
        return AdBannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdBannersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdBanners::route('/'),
            'create' => CreateAdBanner::route('/create'),
            'edit' => EditAdBanner::route('/{record}/edit'),
        ];
    }
}
