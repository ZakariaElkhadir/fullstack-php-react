<?php

namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Schema;
use RuntimeException;
use Throwable;
use App\GraphQL\Schema\SchemaBuilder;

class GraphQL
{
    public static function handle()
    {
        try {
            // Use the comprehensive schema builder instead of basic example
            $schema = SchemaBuilder::build();

            $rawInput = file_get_contents("php://input");
            if ($rawInput === false) {
                throw new RuntimeException("Failed to get php://input");
            }

            $input = json_decode($rawInput, true);
            $query = $input["query"];
            $variableValues = $input["variables"] ?? null;

            // Execute GraphQL query with the proper schema
            $result = GraphQLBase::executeQuery(
                $schema,
                $query,
                null, // rootValue not needed for our resolvers
                null,
                $variableValues,
            );
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                "error" => [
                    "message" => $e->getMessage(),
                ],
            ];
        }

        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        return json_encode($output);
    }
}
