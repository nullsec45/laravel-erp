<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Java Mifi ERP')
            // ->brandLogo(asset('next-logo.png'))
            // ->brandLogoHeight('3rem')
            // ->favicon(asset('next-logo.png'))
            ->darkMode(false)
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
            ])
            ->profile()
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Edit Profile'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                // Core Module
                \Modules\Core\Filament\Resources\CompanySettingResource::class,
                \Modules\Core\Filament\Resources\ActivityLogResource::class,

                // HR Module
                \Modules\HR\Filament\Resources\DepartmentResource::class,
                \Modules\HR\Filament\Resources\EmployeeResource::class,
                \Modules\HR\Filament\Resources\PositionResource::class,

                // Inventory Module
                \Modules\Inventory\Filament\Resources\ProductResource::class,
                \Modules\Inventory\Filament\Resources\CategoryResource::class,
                \Modules\Inventory\Filament\Resources\BrandResource::class,
                \Modules\Inventory\Filament\Resources\UnitResource::class,
                \Modules\Inventory\Filament\Resources\WarehouseResource::class,
                \Modules\Inventory\Filament\Resources\StockMovementResource::class,

                // Sales Module
                \Modules\Sales\Filament\Resources\CustomerResource::class,
                \Modules\Sales\Filament\Resources\QuotationResource::class,
                \Modules\Sales\Filament\Resources\SalesOrderResource::class,
                \Modules\Sales\Filament\Resources\InvoiceResource::class,
                \Modules\Sales\Filament\Resources\PaymentResource::class,

                // Purchasing Module
                \Modules\Purchasing\Resources\VendorResource::class,
                \Modules\Purchasing\Resources\PurchaseOrderResource::class,

                // Logistics Module
                \Modules\Logistic\Filament\Resources\VehicleResource::class,
                \Modules\Logistic\Filament\Resources\DeliveryOrderResource::class,
                \Modules\Logistic\Filament\Resources\RoutePlanningResource::class,
                \Modules\Logistic\Filament\Resources\DriverResource::class,
                \Modules\Logistic\Filament\Resources\ShipmentResource::class,
                \Modules\Logistic\Filament\Resources\TrackingEventResource::class,

            ])
            ->navigationGroups([
                'Dashboard',
                'Sales',
                'Purchasing',
                'Inventory',
                'HR',
                'System',
                'Logistic'
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Dashboard Widgets
                \App\Filament\Widgets\ERPStatsOverview::class,
                \App\Filament\Widgets\SalesChart::class,
                \App\Filament\Widgets\RecentInvoices::class,
                \App\Filament\Widgets\LowStockProducts::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web');
    }
}
