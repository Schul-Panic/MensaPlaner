<?php

require_once __DIR__ . '/../Model/User.php';

class UserController
{
    public function showUsers()
    {
        $userModel = new User();

        $users = $userModel->getAllUsers();

        require __DIR__ . '/../View/users.php';
    }
}