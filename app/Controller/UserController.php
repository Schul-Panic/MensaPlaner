<?php

class UserController
{
    public function index(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Lena'
            ],
            [
                'id' => 2,
                'name' => 'Max'
            ]
        ];
    }
}