<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\AdminController;
use App\Controller\AuthController;
use App\Controller\DishController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteCollectorProxy;

session_start();

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$requireLogin = function (Request $request, RequestHandler $handler) {
    if (empty($_SESSION['account_id'])) {
        return (new SlimResponse())->withHeader('Location', '/')->withStatus(302);
    }

    return $handler->handle($request);
};

$requireAdmin = function (Request $request, RequestHandler $handler) {
    if (empty($_SESSION['account_id'])) {
        return (new SlimResponse())->withHeader('Location', '/')->withStatus(302);
    }

    if (($_SESSION['account_role'] ?? '') !== 'admin') {
        return (new SlimResponse())->withHeader('Location', '/speiseplan')->withStatus(302);
    }

    return $handler->handle($request);
};

$requireStaff = function (Request $request, RequestHandler $handler) {
    if (empty($_SESSION['account_id'])) {
        return (new SlimResponse())->withHeader('Location', '/')->withStatus(302);
    }

    if (!in_array($_SESSION['account_role'] ?? '', ['admin', 'mitarbeiter'], true)) {
        return (new SlimResponse())->withHeader('Location', '/speiseplan')->withStatus(302);
    }

    return $handler->handle($request);
};

$app->get('/', function (Request $request, Response $response) {
    ob_start();
    require __DIR__ . '/../app/View/home.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);

    return $response;
});

$app->post('/', [AuthController::class, 'login']);

$app->get('/register', function (Request $request, Response $response) {
    ob_start();
    require __DIR__ . '/../app/View/register.php';
    $html = ob_get_clean();

    $response->getBody()->write($html);

    return $response;
});

$app->post('/register', [AuthController::class, 'register']);
$app->get('/logout', [AuthController::class, 'logout']);

$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/speiseplan', [DishController::class, 'showDishes']);
    $group->get('/speiseplan/naechste', [DishController::class, 'showNextWeekDishes']);
    $group->post('/speiseplan/naechste/warenkorb', [DishController::class, 'addToCart']);
    $group->get('/speiseplan/warenkorb', [DishController::class, 'showCart']);
    $group->post('/speiseplan/warenkorb/entfernen', [DishController::class, 'removeFromCart']);
    $group->post('/speiseplan/warenkorb/bestellen', [DishController::class, 'placeOrder']);
    $group->get('/speiseplan/naechste-woche', [DishController::class, 'showNextWeekVoting']);
    $group->post('/speiseplan/naechste-woche/vote/{id}/{direction}', [DishController::class, 'voteDish']);
})->add($requireLogin);

$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/speiseplan/bestellungen', [DishController::class, 'showOrderOverview']);
})->add($requireStaff);

$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/users', [AdminController::class, 'showAccounts']);
    $group->get('/users/{id}/edit', [AdminController::class, 'editAccount']);
    $group->post('/users/{id}', [AdminController::class, 'updateAccount']);
    $group->post('/users/{id}/delete', [AdminController::class, 'deleteAccount']);
    $group->post('/users/{id}/impersonate', [AdminController::class, 'impersonate']);
    $group->post('/stop-impersonate', [AdminController::class, 'stopImpersonate']);
})->add($requireAdmin);

$app->run();
