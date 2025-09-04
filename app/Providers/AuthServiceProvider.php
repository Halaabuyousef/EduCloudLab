<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(
            function ($user, $ability) {
                // لو مستخدم ضمن guard admin وعنده دور admin
                if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                    return true;
                }
                return null;
            }
        );
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Verify Your Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email', $url);
        });
    }
}
