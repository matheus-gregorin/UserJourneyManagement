<?php

namespace App\Providers;

use App\Models\UserMysqlModel;
use App\Repository\UserMongoDbRepository;
use App\Repository\UserRepositoryInterface;
use App\Repository\UsersMysqlRepository;
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
        $this->app->bind(UserRepositoryInterface::class, UsersMysqlRepository::class);
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
