<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (!function_exists('mb_split')) {
    function mb_split($pattern, $string, $limit = -1)
    {
        $hasil = @preg_split('/' . str_replace('/', '\/', $pattern) . '/u', $string, $limit);
        return $hasil === false ? false : $hasil;
    }
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
