<?php

namespace App\Providers;

use App\Domain\HttpClients\ClientHttpInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Http\HttpClients\WahaHttpClient;
use App\Http\Responses\ApiResponse;
use App\Repositories\UserMongoDbRepository;
use App\Repositories\UsersMysqlRepository;
use App\Services\UsersServices;
use App\UseCase\ChangeRoleUserUseCase;
use App\UseCase\CreateUserUseCase;
use App\UseCase\GetAllUsersUseCase;
use App\UseCase\LoginUseCase;
use App\UseCase\WebhookReceiveMessageWahaUseCase;
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
        // Response
        $this->app->bind(ApiResponse::class);

        // Services
        $this->app->bind(UsersServices::class);

        // UseCase
        $this->app->bind(LoginUseCase::class);
        $this->app->bind(CreateUserUseCase::class);
        $this->app->bind(GetAllUsersUseCase::class);
        $this->app->bind(ChangeRoleUserUseCase::class);
        $this->app->bind(WebhookReceiveMessageWahaUseCase::class);

        // Interfaces

        // Http Client
        $this->app->bind(ClientHttpInterface::class, WahaHttpClient::class);

        // Repository Adapters
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
