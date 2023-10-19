<?php 
require __DIR__.'/../vendor/autoload.php';
use \App\Utils\View;
use \Dotenv\Dotenv; 
use \WilliamCosta\DatabaseManager; 
use \App\Http\Middleware\Queue as MiddlewareQueue;

require 'vendor/autoload.php';

$dotenv = Dotenv::createUnsafeImmutable(__DIR__."/../");
$dotenv -> load();

DatabaseManager\Database::config(getenv('DB_HOST'), getenv('DB_NAME'),getenv('DB_USER'),getenv('DB_PASS'),getenv('DB_PORT'));

define('URL', getenv('URL'));

View::init([
    'URL' => URL
]);

MiddlewareQueue::setMap([
    'maintenance' => \App\Http\Middleware\Maintenance::class,
    'required-admin-logout' => \App\Http\Middleware\RequireAdminLogout::class,
    'required-admin-login' => \App\Http\Middleware\RequireAdminLogin::class,
    'api' => \App\Http\Middleware\Api::class,
    'user-basic-auth' => \App\Http\Middleware\UserBasicAuth::class,
    'cache' => \App\Http\Middleware\Cache::class
]);

MiddlewareQueue::setDefault([
    'maintenance',
]);
