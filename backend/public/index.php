<?php

// Error handling for production
error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", 1);

try {
    $autoloadPaths = [
        __DIR__ . "/../../vendor/autoload.php",
        __DIR__ . "/../vendor/autoload.php",
        __DIR__ . "/../../../../vendor/autoload.php",
    ];

    $autoloaderFound = false;
    foreach ($autoloadPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $autoloaderFound = true;
            break;
        }
    }

    if (!$autoloaderFound) {
        throw new Exception("Autoloader not found. Checked paths: " . implode(", ", $autoloadPaths));
    }

    // Load environment variables
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
    $dotenv->safeLoad(); // Use safeLoad to not fail if .env doesn't exist in production

    $dispatcher = FastRoute\simpleDispatcher(function (
        FastRoute\RouteCollector $r,
    ) {
        $r->post("/graphql", [App\Controller\GraphQL::class, "handle"]);
        $r->addRoute("OPTIONS", "/graphql", function () {
            // Handle CORS preflight
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type");
            http_response_code(200);
            return "";
        });

        // Add a health check endpoint
        $r->get("/health", function () {
            header("Content-Type: application/json");
            header("Access-Control-Allow-Origin: *");
            return json_encode([
                "status" => "healthy",
                "timestamp" => date("Y-m-d H:i:s"),
                "service" => "GraphQL API",
            ]);
        });

        // Add health_check.php route for Railway
        $r->get("/health_check.php", function () {
            header("Content-Type: application/json");
            header("Access-Control-Allow-Origin: *");
            return json_encode([
                "status" => "healthy",
                "timestamp" => date("Y-m-d H:i:s"),
                "service" => "GraphQL API",
                "note" => "Route-based health check"
            ]);
        });

        // Add database test endpoint
        $r->get("/db_test", function () {
            header("Content-Type: application/json");
            header("Access-Control-Allow-Origin: *");

            try {
                $db = new App\Config\Database();
                $connection = $db->getConnection();

                // Test a simple query
                $stmt = $connection->prepare("SELECT COUNT(*) as count FROM products");
                $stmt->execute();
                $result = $stmt->fetch();

                return json_encode([
                    "status" => "success",
                    "database" => "connected",
                    "product_count" => $result['count'],
                    "timestamp" => date("Y-m-d H:i:s")
                ]);
            } catch (Exception $e) {
                return json_encode([
                    "status" => "error",
                    "database" => "connection_failed",
                    "error" => $e->getMessage(),
                    "timestamp" => date("Y-m-d H:i:s")
                ]);
            }
        });
    });

    $routeInfo = $dispatcher->dispatch(
        $_SERVER["REQUEST_METHOD"],
        $_SERVER["REQUEST_URI"],
    );

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            header("Content-Type: application/json");
            header("Access-Control-Allow-Origin: *");
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "code" => 404,
                "message" => "Endpoint not found",
                "available_endpoints" => ["/graphql", "/health", "/health_check.php", "/db_test"],
            ]);
            break;

        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            header("Content-Type: application/json");
            header("Access-Control-Allow-Origin: *");
            http_response_code(405);
            echo json_encode([
                "status" => "error",
                "code" => 405,
                "message" => "Method not allowed",
                "allowed_methods" => $allowedMethods,
            ]);
            break;

        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            if (is_callable($handler)) {
                echo $handler($vars);
            } else {
                echo call_user_func($handler, $vars);
            }
            break;
    }
} catch (Throwable $e) {
    // Log the error
    error_log(
        "Backend Error: " .
            $e->getMessage() .
            " in " .
            $e->getFile() .
            " on line " .
            $e->getLine(),
    );

    // Return error response
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "code" => 500,
        "message" => "Internal server error",
        "timestamp" => date("Y-m-d H:i:s"),
    ]);
}
