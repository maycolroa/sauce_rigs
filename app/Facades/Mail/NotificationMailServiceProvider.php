<?php

namespace App\Facades\Mail;

use Illuminate\Support\ServiceProvider;

class NotificationMailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('NotificacionMail', NotificationMail::class);
        $this->app->singleton('NotificacionMail', function () {
            return new NotificationMail();
        });
    }
}
