

## Install


```
composer require int/lumen-base
```


### Alter ./bootstrap/app.php

##### Enable Facades

from
```
// $app->withFacades();
```
to 
```
$app->withFacades();
```



##### Alter Exception Handler.

of
```
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::clas
);
```
to
```
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    \Int\Lumen\Core\Exceptions\Handler::class
);
```

##### Add Middleware Accepts Json
```
$app->middleware([
    \Int\Lumen\Core\Http\Middleware\AcceptsJsonMiddleware::class
]);
```


##### Add Transformer Service Provider
```
$app->register(\Int\Lumen\Core\Providers\TransformerServiceProvider::class);
```

##### Add Config to Monolog
```
$app->configureMonologUsing(function ($monolog) {

    $monolog->pushHandler(new \Monolog\Handler\StreamHandler(storage_path() . '/logs/api.log'));
    $monolog->pushProcessor(new \Monolog\Processor\WebProcessor);

    return $monolog;
});
```