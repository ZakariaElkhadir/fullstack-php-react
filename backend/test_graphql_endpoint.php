<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\GraphQL;

// Simulate a GraphQL request
function simulateGraphQLRequest($query, $variables = null) {
    // Backup current input
    $originalInput = $_POST;
    
    // Simulate POST data
    $requestData = ['query' => $query];
    if ($variables) {
        $requestData['variables'] = $variables;
    }
    
    // Create a temporary file to simulate php://input
    $tempFile = tempnam(sys_get_temp_dir(), 'graphql_test');
    file_put_contents($tempFile, json_encode($requestData));
    
    // Override php://input
    $backup = $_SERVER['argv'] ?? null;
    $_SERVER['argv'] = [$tempFile];
    
    // Mock php://input by temporarily modifying the stream
    $originalWrapper = stream_get_wrappers();
    if (in_array('php', $originalWrapper)) {
        stream_wrapper_unregister('php');
    }
    
    // Create a custom stream wrapper for testing
    stream_wrapper_register('php', TestInputWrapper::class);
    TestInputWrapper::$data = json_encode($requestData);
    
    try {
        // Capture output
        ob_start();
        $result = GraphQL::handle();
        $output = ob_get_clean();
        
        return $result;
    } finally {
        // Restore original state
        stream_wrapper_unregister('php');
        if (in_array('php', $originalWrapper)) {
            stream_wrapper_restore('php');
        }
        
        $_SERVER['argv'] = $backup;
        unlink($tempFile);
    }
}

class TestInputWrapper {
    public static $data = '';
    private $position = 0;
    
    public function stream_open($path, $mode, $options, &$opened_path) {
        $this->position = 0;
        return true;
    }
    
    public function stream_read($count) {
        $ret = substr(static::$data, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }
    
    public function stream_eof() {
        return $this->position >= strlen(static::$data);
    }
    
    public function stream_stat() {
        return array();
    }
}

echo "Testing GraphQL Endpoint Integration...\n\n";

// Test 1: Schema introspection
echo "Test 1: Schema Introspection\n";
$introspectionQuery = '
    query IntrospectionQuery {
        __schema {
            queryType {
                name
            }
            mutationType {
                name
            }
        }
    }
';

try {
    $result = simulateGraphQLRequest($introspectionQuery);
    $data = json_decode($result, true);
    
    if (isset($data['data']['__schema'])) {
        echo "✓ Schema introspection successful\n";
        echo "  Query type: " . $data['data']['__schema']['queryType']['name'] . "\n";
        echo "  Mutation type: " . $data['data']['__schema']['mutationType']['name'] . "\n";
    } else {
        echo "✗ Schema introspection failed\n";
        echo "Response: $result\n";
    }
} catch (Exception $e) {
    echo "✗ Error in introspection: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Products query
echo "Test 2: Products Query\n";
$productsQuery = '
    query GetProducts {
        products {
            id
            name
            inStock
            brand
        }
    }
';

try {
    $result = simulateGraphQLRequest($productsQuery);
    $data = json_decode($result, true);
    
    if (isset($data['data']['products'])) {
        echo "✓ Products query successful\n";
        echo "  Found " . count($data['data']['products']) . " products\n";
        if (!empty($data['data']['products'])) {
            $firstProduct = $data['data']['products'][0];
            echo "  First product: " . $firstProduct['name'] . " (ID: " . $firstProduct['id'] . ")\n";
        }
    } else {
        echo "✗ Products query failed\n";
        echo "Response: $result\n";
    }
} catch (Exception $e) {
    echo "✗ Error in products query: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Categories query
echo "Test 3: Categories Query\n";
$categoriesQuery = '
    query GetCategories {
        categories {
            name
        }
    }
';

try {
    $result = simulateGraphQLRequest($categoriesQuery);
    $data = json_decode($result, true);
    
    if (isset($data['data']['categories'])) {
        echo "✓ Categories query successful\n";
        echo "  Found " . count($data['data']['categories']) . " categories\n";
    } else {
        echo "✗ Categories query failed\n";
        echo "Response: $result\n";
    }
} catch (Exception $e) {
    echo "✗ Error in categories query: " . $e->getMessage() . "\n";
}

echo "\nGraphQL Integration Test Complete!\n";
