<?php

namespace Modules\Inventory\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Filament\Resources\ProductResource\Pages;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('sku')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('SKU')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Product Name'),
                        
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->label('Description'),
                        
                        Forms\Components\Select::make('type')
                            ->options([
                                'product' => 'Product',
                                'service' => 'Service',
                                'raw_material' => 'Raw Material',
                            ])
                            ->required()
                            ->default('product')
                            ->label('Type'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),
                        
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('products')
                            ->label('Product Image'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Classification')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('slug')
                                    ->required(),
                                Forms\Components\Textarea::make('description'),
                            ])
                            ->label('Category'),
                        
                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('slug')
                                    ->required(),
                            ])
                            ->label('Brand'),
                        
                        Forms\Components\Select::make('unit_id')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('short_name')
                                    ->required(),
                            ])
                            ->label('Unit'),
                        
                        Forms\Components\TextInput::make('barcode')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Barcode'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('cost_price')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->label('Cost Price'),
                        
                        Forms\Components\TextInput::make('selling_price')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->label('Selling Price'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Stock Management')
                    ->schema([
                        Forms\Components\TextInput::make('minimum_stock')
                            ->numeric()
                            ->default(0)
                            ->label('Minimum Stock'),
                        
                        Forms\Components\TextInput::make('maximum_stock')
                            ->numeric()
                            ->default(0)
                            ->label('Maximum Stock'),
                        
                        Forms\Components\TextInput::make('reorder_level')
                            ->numeric()
                            ->default(0)
                            ->helperText('System will alert when stock reaches this level')
                            ->label('Reorder Level'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->size(50)
                    ->label('Image'),
                
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable()
                    ->label('SKU'),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Product Name'),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->label('Category'),
                
                Tables\Columns\TextColumn::make('brand.name')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable()
                    ->label('Brand'),
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'product' => 'primary',
                        'service' => 'warning',
                        'raw_material' => 'danger',
                        default => 'gray',
                    })
                    ->label('Type'),
                
                Tables\Columns\TextColumn::make('cost_price')
                    ->money('USD')
                    ->sortable()
                    ->label('Cost'),
                
                Tables\Columns\TextColumn::make('selling_price')
                    ->money('USD')
                    ->sortable()
                    ->label('Price'),
                
                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Stock')
                    ->badge()
                    ->color(function ($record): string {
                        $stock = $record->total_stock ?? 0;
                        if ($stock <= 0) return 'danger';
                        if ($stock <= $record->reorder_level) return 'warning';
                        return 'success';
                    }),
                
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                
                Tables\Filters\SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->label('Brand'),
                
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'product' => 'Product',
                        'service' => 'Service',
                        'raw_material' => 'Raw Material',
                    ])
                    ->label('Type'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All products')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn ($query) => $query->lowStock()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
