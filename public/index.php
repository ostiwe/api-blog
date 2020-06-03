<?php

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Headers:content-type');

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__DIR__))->load();

require dirname(__DIR__) . '/libs/db_config.php';


use Blog\Controller\AuthController;
use Blog\Controller\MainController;
use Blog\Middleware\AccessTokenMiddleware;
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$container = new Container();

AppFactory::setContainer($container);
$app = AppFactory::create();


$app->group('/auth', function (RouteCollectorProxy $group) {
	/** @see AuthController::register() */
	$group->post('/register', AuthController::class . ':register');
	/** @see AuthController::login() */
	$group->post('/login', AuthController::class . ':login');
	/** @see AuthController::getInfo() */
	$group->post('/info', AuthController::class . ':getInfo')->add(new AccessTokenMiddleware());
});

/** @see MainController::post() */
$app->post('/posts', MainController::class . ':post');


/**
 * The routing middleware should be added before the ErrorMiddleware
 * Otherwise exceptions thrown from it will not be handled
 */
$app->addRoutingMiddleware();
/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors           -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails     -> Display error details in error log
 *                                  which can be replaced by a callable of your choice.
 *                                  Note: This middleware should be added last. It will not handle any exceptions/errors
 *                                  for middleware added after it.
 */
$app->addErrorMiddleware(getenv('APP_DEBUG'), true, true);

$app->run();