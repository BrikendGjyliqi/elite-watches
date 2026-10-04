<?php

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\InitialsAvatarProvider;
use App\Filament\Resources\ActivityResource;
use App\Http\Controllers\Admin\AcquisitionPulseController;
use Filament\Actions;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->passwordReset()
            ->profile(isSimple: false)
            ->brandName('ÉLITE')
            ->brandLogo(asset('images/logo/elite-nav.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/logo/elite-mark.svg'))
            // Dark is the only mode: forced, so the theme toggle disappears from the user menu.
            ->darkMode(true, isForced: true)
            // Fonts are loaded once via the HEAD_START hook below.
            ->font('Inter', provider: LocalFontProvider::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::hex('#116466'),
                'gray' => [
                    50 => '243, 248, 246',
                    100 => '230, 239, 236',
                    200 => '209, 232, 226',
                    300 => '180, 200, 195',
                    400 => '148, 169, 164',
                    500 => '113, 132, 127',
                    600 => '82, 98, 94',
                    700 => '58, 69, 73',
                    800 => '44, 53, 49',
                    900 => '35, 44, 46',
                    950 => '26, 33, 36',
                ],
                'danger' => Color::hex('#E8A598'),
                'warning' => Color::hex('#D9B08D'),
                'success' => Color::hex('#7FD1A8'),
                'info' => Color::hex('#2A9A9C'),
            ])
            ->sidebarWidth('260px')
            ->collapsibleNavigationGroups()
            ->navigationGroups([
                // The catalog is managed first; acquisitions come in against it.
                NavigationGroup::make('Catalog'),
                NavigationGroup::make('Acquisitions'),
                NavigationGroup::make('Sales'),
                NavigationGroup::make('Customers'),
                NavigationGroup::make('Insights'),
            ])
            ->defaultAvatarProvider(InitialsAvatarProvider::class)
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Profile'),
                MenuItem::make()
                    ->label('Audit log')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->url(fn (): string => ActivityResource::getUrl()),
                'logout' => MenuItem::make()->label('Sign out'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // No Filament account / info widgets — the dashboard lists its own widgets.
            ->widgets([])
            ->databaseNotifications()
            ->databaseNotificationsPolling('20s')
            ->authenticatedRoutes(function (): void {
                Route::get('/acquisitions/pulse', AcquisitionPulseController::class)->name('acquisitions.pulse');
            })
            ->globalSearch(true)
            ->renderHook(PanelsRenderHook::HEAD_START, fn (): string => Blade::render('filament.hooks.fonts'))
            ->renderHook(PanelsRenderHook::TOPBAR_START, fn (): string => Blade::render('filament.hooks.topbar-eyebrow'))
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER, fn (): string => Blade::render('filament.hooks.topbar-clock'))
            ->renderHook(PanelsRenderHook::FOOTER, fn (): string => Blade::render('filament.hooks.footer'))
            ->renderHook(PanelsRenderHook::BODY_END, fn (): string => Blade::render('filament.hooks.motion'))
            ->renderHook(PanelsRenderHook::BODY_END, fn (): string => Blade::render('filament.hooks.acquisition-alerts'))
            ->bootUsing(fn () => $this->configureComponents())
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
            ]);
    }

    /**
     * House style for toasts and destructive confirmations across every admin page.
     */
    protected function configureComponents(): void
    {
        Notifications::alignment(Alignment::End);
        Notifications::verticalAlignment(VerticalAlignment::End);
        Notification::configureUsing(fn (Notification $notification) => $notification->duration(5000));

        $confirmRemoval = fn ($action) => $action
            ->modalHeading('Confirm removal')
            ->modalDescription('This will be permanently removed from the maison’s records. It cannot be undone.')
            ->modalSubmitActionLabel('Remove');

        // "Important" so it runs after each action's own setUp(), which would otherwise reset the heading.
        Actions\DeleteAction::configureUsing($confirmRemoval, isImportant: true);
        Tables\Actions\DeleteAction::configureUsing($confirmRemoval, isImportant: true);
        Tables\Actions\DeleteBulkAction::configureUsing($confirmRemoval, isImportant: true);
    }
}
