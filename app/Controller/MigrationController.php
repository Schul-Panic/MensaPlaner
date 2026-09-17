<?php

namespace App\Controller;

use App\Database;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MigrationController
{
    public function runDishesMigration(Request $request, Response $response): Response
    {
        $sql = file_get_contents(__DIR__ . '/../../migrations/dishes-migration.sql');

        try {
            Database::connection()->exec($sql);
            $message = "OK - Migration erfolgreich.\nTabellen dishes, menu_slots, allergens, dish_allergens wurden neu angelegt und befuellt.";
        } catch (PDOException $exception) {
            $message = "FEHLER bei der Migration:\n" . $exception->getMessage();
        }

        $response->getBody()->write(
            '<pre style="font-family: monospace; padding: 24px; white-space: pre-wrap;">'
            . htmlspecialchars($message)
            . '</pre>'
        );

        return $response;
    }
}
