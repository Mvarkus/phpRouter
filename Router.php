<?php

namespace MVarkus\Routing;

use Closure;
use InvalidArgumentException;

class Router
{
    /**
     * @var \MVarkus\Routing\RouteDictionary
     */
    private RouteDictionary $routeDictionary;

    /**
     * @var \MVarkus\Routing\RouteConfigurationStack
     */
    private RouteConfigurationStack $routeConfigurationStack;

    /**
     * @var \MVarkus\Routing\RoutePathManager
     */
    private RoutePathManager $routePathManager;

    /**
     * @var \MVarkus\Routing\RouteActionHandler
     */
    private RouteActionHandler $routeActionHandler;

    /**
     * @var array
     */
    private array $globalPathConstraints = [];

    /**
     * @param array $routeDependencies (optional)
     */
    public function __construct(array $routeDependencies = [])
    {
        $this->routeDictionary = new RouteDictionary();
        $this->routeConfigurationStack = new RouteConfigurationStack();
        $this->routePathManager = new RoutePathManager();
        $this->routeActionHandler = new RouteActionHandler(
            new DependencyProvider($routeDependencies)
        );
    }

    /**
     * @return array
     */
    public function getGlobalPathConstraints(): array
    {
        return $this->globalPathConstraints;
    }

    /**
     * @param array $pathConstraints
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setGlobalPathConstraints(array $pathConstraints): void
    {
        foreach ($pathConstraints as $name => $pathConstraint) {
            if (!is_string($name)) {
                throw new InvalidArgumentException('$pathConstraints elements must have string keys');
            }
        }

        $this->globalPathConstraints = $pathConstraints;
    }

    /**
     * @param string $method
     * @param string $rawPathPattern
     * @param array|\Closure $action
     * @param array $pathConstraints
     * @param array $defaultParameters
     * @return void
     */
    public function register(
        string $method,
        string $rawPathPattern,
        $action,
        array $pathConstraints = [],
        array $defaultParameters = []
    ): void {
        if ($this->routeConfigurationStack->count() !== 0) {
            $rawPathPattern = $this->routeConfigurationStack->getPrefix() . $rawPathPattern;

            $defaultParameters = array_merge(
                $this->routeConfigurationStack->getDefaultParameters(),
                $defaultParameters
            );

            $pathConstraints = array_merge(
                $this->routeConfigurationStack->getPathConstraints(),
                $pathConstraints
            );
        }

        $this->routeDictionary->add(
            $method,
            new Route(
                $method,
                $this->routePathManager->createPathPattern(
                    $rawPathPattern,
                    array_merge(
                        $this->globalPathConstraints,
                        $pathConstraints
                    )
                ),
                $action,
                $defaultParameters
            )
        );
    }

    /**
     * @param array $config
     * @param \Closure $closure
     * @return void
     */
    public function group(array $config, Closure $closure): void
    {
        $this->routeConfigurationStack->push(
            new RouteConfiguration(
                $config['prefix'] ?? '',
                $config['default'] ?? [],
                $config['with'] ?? []
            )
        );

        $closure();

        $this->routeConfigurationStack->pop();
    }

    /**
     * @param string $method
     * @param string $path
     * @return mixed
     * @throws \MVarkus\Routing\RouteNotFoundException
     */
    public function route(string $method, string $path)
    {
        $routes = $this->routeDictionary->tryGet($method);

        if ($routes === null)
            throw new RouteNotFoundException();

        foreach ($routes as $route) {
            if ($this->routePathManager->match($path, $route->getPathPattern())) {
                return $this->routeActionHandler->execute(
                    $route->getAction(),
                    array_merge(
                        $route->getDefaultParameters(),
                        $this->routePathManager->extractPathParameters($path, $route->getPathPattern())
                    )
                );
            }
        }

        throw new RouteNotFoundException();
    }
}
