<?php

namespace MVarkus\Routing;

use Closure;
use InvalidArgumentException;

class RouteActionHandler
{
    private DependencyProvider $dependencyProvider;

    public function __construct(DependencyProvider $dependencyProvider)
    {
        $this->dependencyProvider = $dependencyProvider;
    }

    /**
     * @param array|\Closure $action
     * @param array $pathParameters
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function execute($action, array $pathParameters)
    {
        if ($action instanceof Closure) {
            return call_user_func_array(
                $action,
                array_merge(
                    $pathParameters,
                    $this->dependencyProvider->getFunctionDependencies($action)
                )
            );
        } elseif (is_array($action)) {
            list($controller, $method) = $action;
            $instance = new $controller(
                ...$this->dependencyProvider->getConstructorDependencies($controller)
            );

            return call_user_func_array(
                [$instance, $method],
                array_merge(
                    $pathParameters,
                    $this->dependencyProvider->getMethodDependencies($instance, $method)
                )
            );
        }

        throw new InvalidArgumentException('Invalid $action type');
    }
}
