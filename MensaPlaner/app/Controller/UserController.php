<?php

require_once 'models/User.php';

class UserController
{
    public function showUsers()
    {
        $userModel = new User();

        $users = $userModel->getAllUsers();

        require 'views/users.php';
    }
}