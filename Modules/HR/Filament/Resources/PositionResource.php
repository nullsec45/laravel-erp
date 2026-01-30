<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Models\Position;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\PositionResource\Pages;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationGroup = 'HR';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Jabatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('department_id')
                    ->relationship(name: 'department', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Jabatan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }
}
