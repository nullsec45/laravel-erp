<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\EmployeeResource\Pages;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'HR';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('employee_id')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_of_birth'),
                    ])->columns(2),

                Forms\Components\Section::make('Employment Details')
                    ->schema([
                        Forms\Components\DatePicker::make('hire_date')
                            ->required(),
                        Forms\Components\DatePicker::make('resign_date')
                            ->required(),
                        Forms\Components\Select::make('department_id')
                            ->relationship(name: 'department', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('position_id')
                            ->relationship(name: 'position', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('salary')
                            ->numeric()
                            ->prefix('$')
                            ->maxValue(999999.99),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'resigned' => 'Resigned',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->getStateUsing(fn($record) => $record->full_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position.name')
                    ->label('Jabatan')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'terminated',
                    ]),
                Tables\Columns\TextColumn::make('hire_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('salary')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->options([
                        'Human Resources' => 'Human Resources',
                        'Information Technology' => 'Information Technology',
                        'Finance' => 'Finance',
                        'Marketing' => 'Marketing',
                        'Sales' => 'Sales',
                        'Operations' => 'Operations',
                        'Legal' => 'Legal',
                        'Customer Service' => 'Customer Service',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'terminated' => 'Terminated',
                    ]),
            ])
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
