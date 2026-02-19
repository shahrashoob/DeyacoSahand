<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

use Illuminate\Support\Facades\DB;

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/
$list=[
    ["ip"=>"62.193.6.29","host"=>"62.193.6.29","env"=>".nasaji_ardakan"],
    ["ip"=>"62.193.6.29:85","host"=>"62.193.6.29:85","env"=>".nasaji_ardakan_test"],
    ["ip"=>"www.ichal.ir","host"=>"ichal.ir","env"=>".ichal"],
    ["ip"=>"192.168.180.190","host"=>"192.168.180.190","env"=>".harirnam"],
    ["ip"=>"192.168.180.190","host"=>"192.168.180.190:85","env"=>".harirnam_test"],
    ["ip"=>"109.125.144.51:8085","host"=>"109.125.144.51:8085","env"=>".harirnam"]
];
//foreach ($list as $item){
//
//    if($_SERVER['HTTP_HOST']==$item["ip"] || $_SERVER['HTTP_HOST']==$item["host"]){
//
//        $app->useEnvironmentPath(base_path()."/.envs/");
//        $app->loadEnvironmentFrom($item["env"]);
//        return $app;
//    }
//}

$app->afterBootstrapping(IlluminateFoundationBootstrapLoadConfiguration::class, function ($ap) {
    // your database connection change may happens here




});
return $app;
