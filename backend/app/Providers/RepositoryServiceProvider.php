<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\AutorRepositoryInterface;
use App\Repositories\AutorRepository;
use App\Repositories\AssuntoRepositoryInterface;
use App\Repositories\AssuntoRepository;
use App\Repositories\LivroRepositoryInterface;
use App\Repositories\LivroRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AutorRepositoryInterface::class, AutorRepository::class);
        $this->app->bind(AssuntoRepositoryInterface::class, AssuntoRepository::class);
        $this->app->bind(LivroRepositoryInterface::class, LivroRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
