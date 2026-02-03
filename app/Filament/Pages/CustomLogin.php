<?php

namespace App\Filament\Pages;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Illuminate\Http\RedirectResponse;

class CustomLogin extends BaseLogin implements HasForms
{
    use \Filament\Forms\Concerns\InteractsWithForms;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ]),
                Actions::make([
                    Action::make('register')
                        ->label('Belum punya akun? Daftar sekarang')
                        ->link()
                        ->url(fn (): string => route('filament.user.auth.register'))
                        ->color('primary')
                        ->extraAttributes(['class' => 'mt-4 justify-center']),
                ])->alignCenter(),
            ])
            ->statePath('data');
    }

    /**
     * Redirect to registration page.
     */
    public function register(): RedirectResponse
    {
        return redirect()->route('filament.user.auth.register');
    }
}

