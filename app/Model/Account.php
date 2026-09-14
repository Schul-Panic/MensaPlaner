<?php

namespace App\Model;

use App\Database;
use PDO;

class Account
{
    public function findByEmail(string $email): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, name, email, password_hash, role FROM accounts WHERE email = :email'
        );
        $statement->execute(['email' => $email]);

        $account = $statement->fetch();

        return $account ?: null;
    }

    public function create(string $name, string $email, string $password, string $role = 'student'): array
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO accounts (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)
             RETURNING id, name, email, role'
        );
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);

        return $statement->fetch();
    }

    public function findById(int $id): ?array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, name, email, role FROM accounts WHERE id = :id'
        );
        $statement->execute(['id' => $id]);

        $account = $statement->fetch();

        return $account ?: null;
    }

    public function all(): array
    {
        $statement = Database::connection()->query(
            'SELECT id, name, email, role, created_at FROM accounts ORDER BY id'
        );

        return $statement->fetchAll();
    }

    public function update(int $id, string $name, string $email, string $role, ?string $password = null): void
    {
        if ($password !== null && $password !== '') {
            $statement = Database::connection()->prepare(
                'UPDATE accounts SET name = :name, email = :email, role = :role, password_hash = :password_hash
                 WHERE id = :id'
            );
            $statement->execute([
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'id' => $id,
            ]);

            return;
        }

        $statement = Database::connection()->prepare(
            'UPDATE accounts SET name = :name, email = :email, role = :role WHERE id = :id'
        );
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $statement = Database::connection()->prepare('DELETE FROM accounts WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
