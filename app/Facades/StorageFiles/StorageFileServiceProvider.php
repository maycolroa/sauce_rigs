<?php

namespace App\Facades\StorageFiles;

use Illuminate\Support\ServiceProvider;

class StorageFileServiceProvider extends ServiceProvider
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
        $this->app->alias('StorageFile', StorageFile::class);
        $this->app->singleton('StorageFile', function () {
            return new StorageFile;
        });
    }
}
