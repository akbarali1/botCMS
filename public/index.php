<?php
error_reporting(E_ALL);                     // Error/Exception engine, always use E_ALL
ini_set('ignore_repeated_errors', true); // always use TRUE
ini_set('display_errors', false);        // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
ini_set('log_errors', true);             // Error/Exception file logging engine.
//ini_set('error_log', __DIR__.'/../public/logs/php_errors.log'); // Logging file path
use App\App;
use App\Middleware\RoleCheckMiddleware;
use App\Middleware\Server;
use App\Middleware\ThrottlingMiddleware;
use App\Middleware\UserExistsMiddleware;

session_start();
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../public/bootstrap.php';

try {

    /**
     * The client code.
     */
    $server = new Server();
    $server->register("admin@example.com", "admin_pass");
    $server->register("user@example.com", "user_pass");
    // All middleware are chained. The client can build various configurations of
    // chains depending on its needs.
    $middleware = new ThrottlingMiddleware(2);
    $middleware
        ->linkWith(new UserExistsMiddleware($server))
        ->linkWith(new RoleCheckMiddleware());

    // The server gets a chain from the client code.
    $server->setMiddleware($middleware);

    do {
        echo "\nEnter your email:\n";
        $email = readline();
        dd($email);
        echo "Enter your password:\n";
        $password = readline();
        $success  = $server->logIn($email, $password);
    } while (!$success);


    //    (new App())->run();
} catch (Throwable $e) {
    dd($e->getMessage(), $e->getLine(), $e->getFile(), $e->getTraceAsString());
}