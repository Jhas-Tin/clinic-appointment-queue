<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Check for maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload Composer dependencies
require __DIR__.'/../vendor/autoload.php';

// Bootstrap the Laravel application
$app = require_once __DIR__.'/../bootstrap/app.php';

// Make the HTTP kernel
$kernel = $app->make(Kernel::class);

// Capture the request and handle it
$request = Request::capture();
$response = $kernel->handle($request);

// Send the response back to the browser
$response->send();

// Terminate the kernel (important for middleware, queues, etc.)
$kernel->terminate($request, $response);
