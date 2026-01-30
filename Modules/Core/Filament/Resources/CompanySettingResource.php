<?php

namespace Modules\Core\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Modules\Core\Models\CompanySetting;
use Modules\Core\Filament\Resources\CompanySettingResource\Pages;

class CompanySettingResource extends Resource
{
    protected static ?string $model = CompanySetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Company Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Company Name'),
                        
                        Forms\Components\TextInput::make('company_email')
                            ->email()
                            ->maxLength(255)
                            ->label('Email'),
                        
                        Forms\Components\TextInput::make('company_phone')
                            ->tel()
                            ->maxLength(255)
                            ->label('Phone'),
                        
                        Forms\Components\Textarea::make('company_address')
                            ->rows(3)
                            ->maxLength(1000)
                            ->label('Address'),
                        
                        Forms\Components\TextInput::make('tax_number')
                            ->maxLength(255)
                            ->label('Tax Number'),
                        
                        Forms\Components\TextInput::make('registration_number')
                            ->maxLength(255)
                            ->label('Registration Number'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Branding')
                    ->schema([
                        Forms\Components\FileUpload::make('company_logo')
                            ->image()
                            ->directory('company')
                            ->label('Company Logo'),
                        
                        Forms\Components\FileUpload::make('company_favicon')
                            ->image()
                            ->directory('company')
                            ->label('Favicon'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Regional Settings')
                    ->schema([
                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'USD - US Dollar',
                                'EUR' => 'EUR - Euro',
                                'GBP' => 'GBP - British Pound',
                                'JPY' => 'JPY - Japanese Yen',
                                'IDR' => 'IDR - Indonesian Rupiah',
                                'MYR' => 'MYR - Malaysian Ringgit',
                                'SGD' => 'SGD - Singapore Dollar',
                            ])
                            ->required()
                            ->label('Currency'),
                        
                        Forms\Components\Select::make('timezone')
                            ->options(timezone_identifiers_list())
                            ->searchable()
                            ->required()
                            ->label('Timezone'),
                        
                        Forms\Components\Select::make('date_format')
                            ->options([
                                'Y-m-d' => 'YYYY-MM-DD (2025-01-10)',
                                'd-m-Y' => 'DD-MM-YYYY (10-01-2025)',
                                'm/d/Y' => 'MM/DD/YYYY (01/10/2025)',
                                'd/m/Y' => 'DD/MM/YYYY (10/01/2025)',
                            ])
                            ->required()
                            ->label('Date Format'),
                        
                        Forms\Components\Select::make('time_format')
                            ->options([
                                'H:i:s' => '24 Hour (14:30:00)',
                                'h:i:s A' => '12 Hour (02:30:00 PM)',
                            ])
                            ->required()
                            ->label('Time Format'),
                        
                        Forms\Components\DatePicker::make('fiscal_year_start')
                            ->label('Fiscal Year Start'),
                        
                        Forms\Components\Select::make('language')
                            ->options([
                                'en' => 'English',
                                'id' => 'Bahasa Indonesia',
                                'ms' => 'Bahasa Malaysia',
                            ])
                            ->required()
                            ->label('Language'),
                        
                        Forms\Components\Select::make('theme')
                            ->options([
                                'light' => 'Light',
                                'dark' => 'Dark',
                            ])
                            ->required()
                            ->label('Theme'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->label('Company Name'),
                
                Tables\Columns\TextColumn::make('company_email')
                    ->searchable()
                    ->label('Email'),
                
                Tables\Columns\TextColumn::make('currency')
                    ->badge()
                    ->label('Currency'),
                
                Tables\Columns\TextColumn::make('timezone')
                    ->label('Timezone'),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Updated'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCompanySettings::route('/'),
        ];
    }
}
