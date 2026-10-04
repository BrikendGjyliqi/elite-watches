<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Branded split-screen login for the ÉLITE admin panel.
 *
 * Only presentation is overridden — authentication, rate limiting and
 * validation are inherited untouched from Filament's base Login page.
 */
class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    protected static string $layout = 'filament.layouts.elite-auth';

    public function getHeading(): string | Htmlable
    {
        return 'Sign in to continue';
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->placeholder('keeper@elite.com')
            ->extraInputAttributes(['aria-label' => 'Email address'], merge: true);
    }

    protected function getPasswordFormComponent(): Component
    {
        /** @var TextInput $component */
        $component = parent::getPasswordFormComponent();

        // The reset link is rendered below the submit button instead of as a label hint.
        return $component
            ->hint(null)
            ->placeholder('••••••••••')
            ->extraInputAttributes(['aria-label' => 'Password'], merge: true);
    }

    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()
            ->extraInputAttributes(['aria-label' => 'Remember me on this device'], merge: true);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Enter the Vault')
            ->extraAttributes(['class' => 'elite-submit']);
    }
}
