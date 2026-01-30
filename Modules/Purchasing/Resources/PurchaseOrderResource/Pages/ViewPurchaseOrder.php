<?php

namespace Modules\Purchasing\Resources\PurchaseOrderResource\Pages;

use Modules\Purchasing\Resources\PurchaseOrderResource;
use Modules\Purchasing\Models\PurchaseOrder;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (PurchaseOrder $record) => $record->status === 'draft'),
            Actions\Action::make('approve')
                ->label('Approve PO')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (PurchaseOrder $record) => $record->status === 'pending')
                ->action(function (PurchaseOrder $record) {
                    $record->update(['status' => 'approved']);
                    $this->refreshFormData(['status']);
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Purchase Order Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('po_number')
                            ->label('PO Number')
                            ->copyable()
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('vendor.name')
                            ->label('Vendor'),
                        Infolists\Components\TextEntry::make('order_date')
                            ->date(),
                        Infolists\Components\TextEntry::make('expected_delivery_date')
                            ->date()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'pending' => 'warning',
                                'approved' => 'info',
                                'partial' => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            }),
                        Infolists\Components\TextEntry::make('payment_terms')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Created By'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Items')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name')
                                    ->label('Product'),
                                Infolists\Components\TextEntry::make('description')
                                    ->placeholder('—'),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->numeric(decimalPlaces: 2),
                                Infolists\Components\TextEntry::make('unit_price')
                                    ->money('USD'),
                                Infolists\Components\TextEntry::make('discount_rate')
                                    ->suffix('%'),
                                Infolists\Components\TextEntry::make('tax_rate')
                                    ->suffix('%'),
                                Infolists\Components\TextEntry::make('total_price')
                                    ->label('Total')
                                    ->money('USD')
                                    ->weight('bold'),
                            ])
                            ->columns(7),
                    ]),

                Infolists\Components\Section::make('Totals')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->money('USD'),
                                Infolists\Components\TextEntry::make('discount_amount')
                                    ->money('USD'),
                                Infolists\Components\TextEntry::make('tax_amount')
                                    ->money('USD'),
                                Infolists\Components\TextEntry::make('shipping_cost')
                                    ->money('USD'),
                                Infolists\Components\TextEntry::make('total_amount')
                                    ->money('USD')
                                    ->weight('bold')
                                    ->size('lg'),
                                Infolists\Components\TextEntry::make('outstanding_amount')
                                    ->money('USD')
                                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                                    ->weight('bold'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('shipping_address')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('notes')
                            ->placeholder('—'),
                    ])
                    ->columns(1),
            ]);
    }
}
