

##Install

in composer.json, add code
```json
"repositories": [
    {
      "type": "vcs",
      "url": "git@bitbucket.org:devsintegrandose/int-lumen-base.git"
    }
  ]
```

after exec in bash
 ```bash
composer require int/lumen-base
```


### Alter ./bootstrap/app.php

#####Enable Facades

of
```php
// $app->withFacades();
```
to 
```php
$app->withFacades();
```



#####Alter Exception Handler.

of
```php
$app->sngleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::clas
);
```
to
```php
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    \Int\Lumen\Core\Exceptions\Handler::class
);
```

#####Add Middleware Accepts Json
```php
$app->middleware([
    \Int\Lumen\Core\Http\Middleware\AcceptsJsonMiddleware::class
]);
```


#####Add Transformer Service Provider
```php
$app->register(\Int\Lumen\Core\Providers\TransformerServiceProvider::class);
```

#####Add Config to Monolog
```php
$app->configureMonologUsing(function ($monolog) {

    $monolog->pushHandler(new \Monolog\Handler\StreamHandler(storage_path() . '/logs/api.log'));
    $monolog->pushProcessor(new \Monolog\Processor\WebProcessor);

    return $monolog;
});
```