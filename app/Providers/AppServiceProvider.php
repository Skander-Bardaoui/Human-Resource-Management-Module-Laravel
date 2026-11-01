<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            $expire = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

            $data = [
                'url' => $url,
                'name' => $notifiable->name,
                'email' => $notifiable->getEmailForPasswordReset(),
                'expire' => $expire,
            ];

            return (new MailMessage)
                ->subject('Réinitialisation de votre mot de passe — HRM Pro')
                ->view('emails.reset-password', $data)
                ->text('emails.reset-password-text', $data);
        });
    }
}
