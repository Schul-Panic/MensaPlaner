<?php

namespace App\Controller;

use App\Model\Account;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminController
{
    public const ROLES = ['admin', 'teacher', 'student', 'gast', 'mitarbeiter'];

    public function showAccounts(Request $request, Response $response): Response
    {
        $accounts = (new Account())->all();

        ob_start();
        require __DIR__ . '/../View/accounts.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function editAccount(Request $request, Response $response, array $args): Response
    {
        $account = (new Account())->findById((int) $args['id']);

        if (!$account) {
            return $response->withHeader('Location', '/users')->withStatus(302);
        }

        ob_start();
        require __DIR__ . '/../View/account-edit.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function updateAccount(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $data = $request->getParsedBody();
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $role = $data['role'] ?? '';
        $password = trim($data['password'] ?? '');

        if ($name === '' || $email === '' || !in_array($role, self::ROLES, true)) {
            $_SESSION['flash_error'] = 'Bitte alle Felder gültig ausfüllen.';

            return $response->withHeader('Location', "/users/{$id}/edit")->withStatus(302);
        }

        try {
            (new Account())->update($id, $name, $email, $role, $password ?: null);
        } catch (PDOException $exception) {
            $_SESSION['flash_error'] = 'Diese E-Mail-Adresse wird schon verwendet.';

            return $response->withHeader('Location', "/users/{$id}/edit")->withStatus(302);
        }

        return $response->withHeader('Location', '/users')->withStatus(302);
    }

    public function deleteAccount(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];

        if ($id !== (int) $_SESSION['account_id']) {
            (new Account())->delete($id);
        }

        return $response->withHeader('Location', '/users')->withStatus(302);
    }
}
