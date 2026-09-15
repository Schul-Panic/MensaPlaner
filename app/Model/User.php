<?php

namespace App\Model;

class User
{
    public function getAllUsers()
    {
        return [
            [
                "id" => 1,
                "name" => "Lena"
            ],
            [
                "id" => 2,
                "name" => "Max"
            ],
            [
                "id" => 3,
                "name" => "Vlada"
            ]
        ];
    }
}