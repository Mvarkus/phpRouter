# phpRouter
PHP router which navigates request through an application.

## Examples

Create router instance with optional dependencies.
```php
use MVarkus\Routing\Router;
use App\Services\BooksService;
use MVarkus\Routing\Dependency;
use MVarkus\Routing\DependencyType;
use App\Interfaces\BooksServiceInterface;
use Symfony\Component\HttpFoundation\Request;

$router = new Router([
    new Dependency(Request::class, fn () => Request::createFromGlobals(), DependencyType::Singleton),
    new Dependency(BooksServiceInterface::class, BooksService::class, DependencyType::Transient)
]);
```
Set global path constraints.
```php
$router->setGlobalPathConstraints(['userId' => '\d+']);
```

Register route with a callback.
```php
$router->register('GET', '/health', function (): array {
    return [
        'healthy' => true,
        'services' => [
            'serviceA' => [
                'healthy' => true,
            ],
            'serviceB' => [
                'healthy' => true,
            ],
            'serviceC' => [
                'healthy' => true,
            ]
        ]
    ];
});
```

Register route with path parameter.
```php
$router->register('GET', '/health/{service}', function (string $service): array {
    return [
        [
            'service' => $service,
            'healthy' => true 
        ]
    ];
}, ['service' => '[a-zA-Z]+']);
```

Register route with default parameter.
```php
$router->register('GET', '/dashboard/{dashboardId?}', function (string $dashboardId): array {
    return [
        [
            'dashboardId' => $dashboardId,
            'summary' => []
        ]
    ];
}, ['dashboardId' => '[a-zA-Z0-9\-]+'], ['dashboardId' => 'default']);
```

Group related routes.
```php
$router->group(
    [
        'prefix' => '/rooms/{roomId?}',
        'with' => ['roomId' => '[a-zA-Z0-9\-]+'],
        'default' => ['roomId' => 'default']
    ],
    function () use ($router) {
        $router->register(
            'GET',
            '/users',
            function (string $roomId): string {
                return "List of users in room {$roomId}...";
            }
        );

        $router->register(
            'GET',
            '/users/{userId}',
            function (string $roomId, int $userId): string {
                return "Show user with id {$userId} in {$roomId} room...";
            }
        );

        $router->register(
            'POST',
            '/users',
            function (string $roomId, Request $request): string {
                $payload = $request->toArray();

                return "Add user to {$roomId} room with name {$payload['name']}...";
            }
        );

        $router->register(
            'PUT',
            '/users/{userId}',
            function (string $roomId, int $userId, Request $request): string {
                $payload = $request->toArray();

                return "Update user {$userId} in {$roomId} room with new name {$payload['name']}...";
            }
        );

        $router->register(
            'DELETE',
            '/users/{userId}',
            function (string $roomId, int $userId): string {
                return "Remove user {$userId} from {$roomId} room...";
            }
        );
    }
);
```
Register route with a controller.
```php
use App\Controllers\BooksController;

$router->register('GET', '/books', [BooksController::class, 'index']);
```
Route request.
```php
try {
    
    $result = $router->route($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'])['path']);

    // Output result...

} catch (RouteNotFoundException $exception) {
    
    // Handle route not found...

} catch (Exception $exception) {
    
    // Handle error...

}
```
