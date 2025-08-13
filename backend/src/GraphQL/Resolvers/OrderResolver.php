<?php

namespace App\GraphQL\Resolvers;

use App\Config\Database;
use PDO;

/**
 * OrderResolver class
 */
class OrderResolver
{
    /**
     * Create a new order (GraphQL Mutation)
     */
    public static function createOrder($rootValue, array $args): array
    {
        $orderData = $args["orderInput"];

        try {
            $db = new Database();
            $connection = $db->getConnection();

            $connection->beginTransaction();

            $orderId = "order_" . uniqid();

            $orderSql = "INSERT INTO orders (id, customer_email, total_amount, currency, status, created_at)
                        VALUES (?, ?, ?, ?, ?, ?)";

            $orderStmt = $connection->prepare($orderSql);
            $orderStmt->execute([
                $orderId,
                $orderData["customerEmail"],
                $orderData["totalAmount"],
                $orderData["currency"] ?? "USD",
                "pending",
                date("Y-m-d H:i:s"),
            ]);

            $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, selected_attributes, price)
                       VALUES (?, ?, ?, ?, ?)";

            $itemStmt = $connection->prepare($itemSql);

            foreach ($orderData["items"] as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item["productId"],
                    $item["quantity"],
                    json_encode($item["selectedAttributes"] ?? []),
                    $item["price"],
                ]);
            }

            $connection->commit();
            $db->close();

            return [
                "success" => true,
                "orderId" => $orderId,
                "message" => "Order created successfully",
            ];
        } catch (\Exception $e) {
            if (isset($connection)) {
                $connection->rollBack();
            }

            error_log("Order creation error: " . $e->getMessage());

            return [
                "success" => false,
                "orderId" => null,
                "message" => "Failed to create order: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Get order by ID
     */
    public static function getOrderById($rootValue, array $args): ?array
    {
        $orderId = $args["id"];

        try {
            $db = new Database();
            $connection = $db->getConnection();

            $orderSql = "SELECT * FROM orders WHERE id = ?";
            $orderStmt = $connection->prepare($orderSql);
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return null;
            }

            $itemsSql = "SELECT * FROM order_items WHERE order_id = ?";
            $itemsStmt = $connection->prepare($itemsSql);
            $itemsStmt->execute([$orderId]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            $db->close();

            return [
                "id" => $order["id"],
                "customerEmail" => $order["customer_email"],
                "totalAmount" => (float) $order["total_amount"],
                "currency" => $order["currency"],
                "status" => $order["status"],
                "createdAt" => $order["created_at"],
                "items" => array_map(function ($item) {
                    return [
                        "productId" => $item["product_id"],
                        "quantity" => (int) $item["quantity"],
                        "selectedAttributes" =>
                            json_decode($item["selected_attributes"], true) ??
                            [],
                        "price" => (float) $item["price"],
                    ];
                }, $items),
            ];
        } catch (\Exception $e) {
            error_log("Order retrieval error: " . $e->getMessage());
            return null;
        }
    }
}
