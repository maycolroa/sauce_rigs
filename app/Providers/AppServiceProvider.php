<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Services\ConfigurationServices;
use App\Services\ConstantService;
use App\Services\PermissionService;
use App\Services\ViewService;
use App\Managers\Administrative\KeywordManager;
use App\Managers\System\NotificationManager;
use App\Managers\RiskMatrixManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        View::share('viewService', new ViewService());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('bugsnag.multi', \Illuminate\Contracts\Logging\Log::class);
        $this->app->alias('bugsnag.multi', \Psr\Log\LoggerInterface::class);

        $this->app->singleton('configuration_service', function () {
            return new ConfigurationServices();
        });

        $this->app->singleton('constant_service', function () {
            return new ConstantService();
        });

        $this->app->singleton('permission_service', function () {
            return new PermissionService();
        });

        $this->app->singleton('keyword_manager', function () {
            return new KeywordManager();
        });

        $this->app->singleton('notification_manager', function () {
            return new NotificationManager();
        });

        $this->app->singleton('risk_matrix_manager', function () {
            return new RiskMatrixManager();
        });
    }
}
