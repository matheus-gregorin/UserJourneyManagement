<?php

namespace App\Providers;

use App\Repository\UserRepositoryInterface;
use App\Repository\UsersRepository;
use App\Services\UsersServices;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Definir aqui as services
        $this->app->bind(UsersServices::class);

        //Definir aqui interfaces
        $this->app->bind(UserRepositoryInterface::class, UsersRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
