<?php

namespace App\Controller;

use App\Model\Account;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        $account = (new Account())->findByEmail($email);

        if (!$account || !password_verify($password, $account['password_hash'])) {
            $_SESSION['flash_error'] = 'E-Mail oder Passwort ist falsch.';

            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $_SESSION['account_id'] = $account['id'];
        $_SESSION['account_name'] = $account['name'];
        $_SESSION['account_role'] = $account['role'];

        return $response->withHeader('Location', '/speiseplan')->withStatus(302);
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $passwordConfirm = $data['password_confirm'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Bitte alle Felder ausfüllen.';

            return $response->withHeader('Location', '/register')->withStatus(302);
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['flash_error'] = 'Die Passwörter stimmen nicht überein.';

            return $response->withHeader('Location', '/register')->withStatus(302);
        }

        try {
            $account = (new Account())->create($name, $email, $password);
        } catch (PDOException $exception) {
            $_SESSION['flash_error'] = 'Diese E-Mail-Adresse wird schon verwendet.';

            return $response->withHeader('Location', '/register')->withStatus(302);
        }

        $_SESSION['account_id'] = $account['id'];
        $_SESSION['account_name'] = $account['name'];
        $_SESSION['account_role'] = $account['role'];

        return $response->withHeader('Location', '/speiseplan')->withStatus(302);
    }

    public function logout(Request $request, Response $response): Response
    {
        unset($_SESSION['account_id'], $_SESSION['account_name'], $_SESSION['account_role']);

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
