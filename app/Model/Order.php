<?php

namespace App\Model;

use App\Database;

class Order
{
    public function create(int $accountId, array $items): int
    {
        $db = Database::connection();
        $db->beginTransaction();

        $orderStatement = $db->prepare(
            'INSERT INTO orders (account_id) VALUES (:account_id) RETURNING id'
        );
        $orderStatement->execute(['account_id' => $accountId]);
        $orderId = (int) $orderStatement->fetchColumn();

        $itemStatement = $db->prepare(
            'INSERT INTO order_items (order_id, dish_name, price, quantity)
             VALUES (:order_id, :dish_name, :price, :quantity)'
        );

        foreach ($items as $item) {
            $itemStatement->execute([
                'order_id' => $orderId,
                'dish_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ]);
        }

        $db->commit();

        return $orderId;
    }

    public function forAccount(int $accountId): array
    {
        $statement = Database::connection()->prepare(
            'SELECT id, created_at FROM orders WHERE account_id = :account_id ORDER BY created_at DESC'
        );
        $statement->execute(['account_id' => $accountId]);
        $orders = $statement->fetchAll();

        foreach ($orders as &$order) {
            $itemsStatement = Database::connection()->prepare(
                'SELECT dish_name, price, quantity FROM order_items WHERE order_id = :order_id'
            );
            $itemsStatement->execute(['order_id' => $order['id']]);
            $order['items'] = $itemsStatement->fetchAll();
        }

        return $orders;
    }
}
