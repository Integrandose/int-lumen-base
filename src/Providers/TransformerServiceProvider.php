<?php

namespace Int\Lumen\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Int\Lumen\Core\Transformer\DataArraySerializer;
use League\Fractal\Manager;

class TransformerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Manager::class, function ($app) {
            $manager = new Manager();
            $manager->setSerializer(new DataArraySerializer());
            return $manager;
        });

        $this->app->alias(Manager::class, 'fractal');
    }
}
