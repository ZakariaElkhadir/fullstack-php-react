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
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException("Invalid JSON input: " . json_last_error_msg());
            }
            
            if (!isset($input["query"])) {
                throw new RuntimeException("Missing 'query' field in request");
            }
            
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
            // Log the error for debugging
            error_log("GraphQL Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            
            $output = [
                "errors" => [
                    [
                        "message" => $e->getMessage(),
                        "extensions" => [
                            "code" => "INTERNAL_ERROR",
                            "file" => $e->getFile(),
                            "line" => $e->getLine()
                        ]
                    ]
                ],
                "data" => null
            ];
        }

        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        return json_encode($output, JSON_PRETTY_PRINT);
    }
}
