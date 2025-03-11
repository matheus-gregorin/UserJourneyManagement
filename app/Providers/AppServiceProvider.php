<?php

namespace App\Providers;

use App\Models\UserMongoDbModel;
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
        //Services
        $this->app->bind(UsersServices::class);

        //Interfaces
        //$this->app->bind(UserRepositoryInterface::class, UsersMysqlRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserMongoDbRepository::class);
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
