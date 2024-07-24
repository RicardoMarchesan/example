<?php

namespace App\Providers;

use App\Repositories\Eloquent\InputRepository;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(InputRepositoryInterface::class, InputRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
