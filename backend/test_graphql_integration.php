<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\GraphQL\Schema\SchemaBuilder;
use GraphQL\GraphQL;
use GraphQL\Type\Introspection;

try {
    echo "Testing GraphQL Schema Integration...\n\n";
    
    // Build the schema
    $schema = SchemaBuilder::build();
    echo "✓ Schema built successfully\n";
    
    // Test introspection query to see if schema is valid
    $introspectionQuery = Introspection::getIntrospectionQuery();
    $result = GraphQL::executeQuery($schema, $introspectionQuery);
    
    if (empty($result->errors)) {
        echo "✓ Schema introspection successful\n";
        
        // Test a simple query to check if it's executable
        $testQuery = '
            query {
                __schema {
                    queryType {
                        name
                        fields {
                            name
                            description
                        }
                    }
                    mutationType {
                        name
                        fields {
                            name
                            description
                        }
                    }
                }
            }
        ';
        
        $result = GraphQL::executeQuery($schema, $testQuery);
        
        if (empty($result->errors)) {
            echo "✓ Test query executed successfully\n\n";
            
            $data = $result->toArray();
            if (isset($data['data']['__schema']['queryType']['fields'])) {
                echo "Available Query Fields:\n";
                foreach ($data['data']['__schema']['queryType']['fields'] as $field) {
                    echo "  - {$field['name']}: {$field['description']}\n";
                }
            }
            
            if (isset($data['data']['__schema']['mutationType']['fields'])) {
                echo "\nAvailable Mutation Fields:\n";
                foreach ($data['data']['__schema']['mutationType']['fields'] as $field) {
                    echo "  - {$field['name']}: {$field['description']}\n";
                }
            }
        } else {
            echo "✗ Test query failed:\n";
            foreach ($result->errors as $error) {
                echo "  - " . $error->getMessage() . "\n";
            }
        }
    } else {
        echo "✗ Schema introspection failed:\n";
        foreach ($result->errors as $error) {
            echo "  - " . $error->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
