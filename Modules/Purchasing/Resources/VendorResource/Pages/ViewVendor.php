<?php

namespace Modules\Purchasing\Resources\VendorResource\Pages;

use Modules\Purchasing\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Vendor Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('code')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('contact_person')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('email')
                            ->copyable()
                            ->icon('heroicon-m-envelope')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('phone')
                            ->icon('heroicon-m-phone')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('mobile')
                            ->icon('heroicon-m-device-phone-mobile')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('website')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('tax_id')
                            ->label('Tax ID')
                            ->placeholder('—'),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Financial Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_terms')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('credit_limit')
                            ->money('USD'),
                        Infolists\Components\TextEntry::make('total_purchases')
                            ->label('Total Purchases')
                            ->money('USD')
                            ->color('success')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('outstanding_balance')
                            ->label('Outstanding Balance')
                            ->money('USD')
                            ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                            ->weight('bold'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Address')
                    ->schema([
                        Infolists\Components\TextEntry::make('address')
                            ->placeholder('—'),
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('city')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('state')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('country')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('postal_code')
                                    ->placeholder('—'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
