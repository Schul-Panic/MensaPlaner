<?php

require_once __DIR__ . '/../app/Controller/UserController.php';

$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($route) {

    case '/':
        echo "Startseite";
        break;

    case '/users':
        $controller = new UserController();
        $controller->showUsers();
        break;

    default:
        http_response_code(404);
        echo 'Seite nicht gefunden';
}