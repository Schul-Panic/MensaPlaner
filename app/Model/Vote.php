<?php

namespace App\Model;

use App\Database;

class Vote
{
    public function resultsForDishIds(array $dishIds): array
    {
        if (!$dishIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($dishIds), '?'));
        $statement = Database::connection()->prepare(
            "SELECT dish_id, direction, COUNT(*) AS total
             FROM votes
             WHERE dish_id IN ($placeholders)
             GROUP BY dish_id, direction"
        );
        $statement->execute($dishIds);

        $results = [];
        foreach ($statement->fetchAll() as $row) {
            $results[(int) $row['dish_id']][$row['direction']] = (int) $row['total'];
        }

        return $results;
    }

    public function userVotes(int $accountId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT dish_id, direction FROM votes WHERE account_id = :account_id'
        );
        $statement->execute(['account_id' => $accountId]);

        $votes = [];
        foreach ($statement->fetchAll() as $row) {
            $votes[(int) $row['dish_id']] = $row['direction'];
        }

        return $votes;
    }

    public function cast(int $dishId, int $accountId, string $direction): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO votes (dish_id, account_id, direction)
             VALUES (:dish_id, :account_id, :direction)
             ON CONFLICT (dish_id, account_id)
             DO UPDATE SET direction = EXCLUDED.direction, created_at = NOW()'
        );
        $statement->execute([
            'dish_id' => $dishId,
            'account_id' => $accountId,
            'direction' => $direction,
        ]);
    }
}
