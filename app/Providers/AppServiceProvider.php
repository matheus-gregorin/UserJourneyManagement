<?php

namespace App\Providers;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Repositories\UserMongoDbRepository;
use App\Repositories\UsersMysqlRepository;
use App\Services\UsersServices;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
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
        switch (Config::get('database.default')) {
            case 'mysql':
                    $this->app->bind(UserRepositoryInterface::class, UsersMysqlRepository::class);
                break;
            case 'mongodb':
                    $this->app->bind(UserRepositoryInterface::class, UserMongoDbRepository::class);
                break;
            default:
                    Log::critical("DB undefined", Config::get('database.default'));
                break;
        }
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
