<?php

namespace App\Filament\Resources\Consultants;

use App\Enums\ConsultantStatus;
use App\Filament\Resources\Consultants\Pages\EditConsultant;
use App\Filament\Resources\Consultants\Pages\ListConsultants;
use App\Filament\Resources\Consultants\Schemas\ConsultantForm;
use App\Filament\Resources\Consultants\Tables\ConsultantsTable;
use App\Models\ConsultantProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ConsultantResource extends Resource
{
    protected static ?string $model = ConsultantProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Marketplace';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Consultants';

    protected static ?string $modelLabel = 'Consultant';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', ConsultantStatus::Pending)->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pending approval';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'user.email', 'slug'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var ConsultantProfile $record */
        return [
            'Name' => $record->user->name,
            'Status' => $record->status->label(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ConsultantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsultantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsultants::route('/'),
            'edit' => EditConsultant::route('/{record}/edit'),
        ];
    }
}
