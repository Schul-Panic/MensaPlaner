<?php

require_once 'controllers/UserController.php';

$route = $_SERVER['REQUEST_URI'];

switch ($route)
{
    case '/users':

        $controller = new UserController();

        $controller->showUsers();

        break;

    default:

        echo "404 Seite nicht gefunden";
}