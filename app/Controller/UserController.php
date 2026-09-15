<?php

namespace App\Controller;

use App\Model\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    public function showUsers(Request $request, Response $response): Response
    {
        $users = $this->currentUsers();

        ob_start();
        require __DIR__ . '/../View/users.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function addUser(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $name = trim($data['name'] ?? '');

        if ($name !== '') {
            $_SESSION['extra_users'][] = [
                'id' => $this->nextUserId(),
                'name' => $name,
            ];
        }

        return $response->withHeader('Location', '/users')->withStatus(302);
    }

    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        $_SESSION['deleted_ids'][] = (int) $args['id'];

        return $response->withHeader('Location', '/users')->withStatus(302);
    }

    private function nextUserId(): int
    {
        if (!isset($_SESSION['next_id'])) {
            $userModel = new User();
            $baseIds = array_column($userModel->getAllUsers(), 'id');
            $_SESSION['next_id'] = $baseIds ? max($baseIds) + 1 : 1;
        }

        return $_SESSION['next_id']++;
    }

    private function currentUsers(): array
    {
        $userModel = new User();
        $users = array_merge($userModel->getAllUsers(), $_SESSION['extra_users'] ?? []);

        $deletedIds = $_SESSION['deleted_ids'] ?? [];

        return array_values(array_filter(
            $users,
            fn (array $user) => !in_array($user['id'], $deletedIds, true)
        ));
    }
}
