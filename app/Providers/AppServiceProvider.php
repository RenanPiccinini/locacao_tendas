<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $publicStorage = public_path('storage');
            if (!file_exists($publicStorage) && !is_link($publicStorage)) {
                @symlink(storage_path('app/public'), $publicStorage);
            }
        } catch (\Throwable $e) {
            // Silencioso: rota de fallback servirArquivoStorage trata o fornecimento dos arquivos
        }
    }
}
