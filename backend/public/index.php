<?php

require_once __DIR__ . '/../vendor/autoload.php';
echo "the page works well";
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
  $r->post('/graphql', [App\Controller\GraphQL::class, 'handle']);
});
/**
 * test with rest
 */
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->post('/graphql', [App\Controller\GraphQL::class, 'handle']);
    // Add this line for REST test endpoint
    $r->get('/api/products', function () {
        header('Content-Type: application/json');
        try {
            $products = \App\Models\Product::findAll();
            echo json_encode(\App\Models\Product::toArrayCollection($products));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        return '';
    });
});
/*===============================================================*/

$routeInfo = $dispatcher->dispatch(
  $_SERVER['REQUEST_METHOD'],
  $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
  case FastRoute\Dispatcher::NOT_FOUND:
    // ... 404 Not Found
    break;
  case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
    $allowedMethods = $routeInfo[1];
    // ... 405 Method Not Allowed
    break;
  case FastRoute\Dispatcher::FOUND:
    $handler = $routeInfo[1];
    $vars = $routeInfo[2];
    echo $handler($vars);
    break;
}
