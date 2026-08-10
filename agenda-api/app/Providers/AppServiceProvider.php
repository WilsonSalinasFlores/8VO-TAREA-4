<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\UsuarioRepository;
use App\Repositories\Contracts\ContactoRepositoryInterface;
use App\Repositories\ContactoRepository;
use App\Repositories\Contracts\PreguntaRepositoryInterface;
use App\Repositories\PreguntaRepository;
use App\Repositories\Contracts\BitacoraRepositoryInterface;
use App\Repositories\BitacoraRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(ContactoRepositoryInterface::class, ContactoRepository::class);
        $this->app->bind(PreguntaRepositoryInterface::class, PreguntaRepository::class);
        $this->app->bind(BitacoraRepositoryInterface::class, BitacoraRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
