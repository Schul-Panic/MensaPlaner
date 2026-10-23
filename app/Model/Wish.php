<?php

namespace App\Model;

use App\Database;

class Wish
{
    public const MAX_LENGTH = 500;

    public function create(int $accountId, string $text): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO wishes (account_id, text) VALUES (:account_id, :text)'
        );
        $statement->execute(['account_id' => $accountId, 'text' => $text]);
    }

    public function all(): array
    {
        // LEFT JOIN, damit Wünsche gelöschter Accounts sichtbar bleiben
        $statement = Database::connection()->query(
            'SELECT wishes.text, wishes.created_at, accounts.name AS account_name
             FROM wishes
             LEFT JOIN accounts ON accounts.id = wishes.account_id
             ORDER BY wishes.created_at DESC, wishes.id DESC'
        );

        return $statement->fetchAll();
    }
}
