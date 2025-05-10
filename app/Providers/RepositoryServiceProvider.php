<?php

namespace App\Providers;

use App\Interfaces\AccoutingRepositoryInterface;
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\SharingRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use App\Repositories\AccountingRepository;
use App\Repositories\AuthRepository;
use App\Repositories\SharingRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(AccoutingRepositoryInterface::class, AccountingRepository::class);
        $this->app->bind(SharingRepositoryInterface::class, SharingRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
