<?php

require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Helper/Debug.php';

class UserController
{
    public function showUsers()
    {
        $userModel = new User();

        $users = $userModel->getAllUsers();
        Debug::varDumpAndDiePre($users);

        require __DIR__ . '/../View/users.php';
    }
}