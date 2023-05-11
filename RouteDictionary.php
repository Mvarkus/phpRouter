<?php

namespace MVarkus\Routing;

class RouteDictionary
{
    /**
     * @var array
     */
    private array $routes = [];

    /**
     * @param string $offset
     * @return array
     */
    public function tryGet(string $offset): ?array
    {
        return $this->routes[$offset] ?? null;
    }

    /**
     * @param \MVarkus\Routing\Route $route
     * @return void
     */
    public function add(string $offset, Route $route): void
    {
        if (!key_exists($offset, $this->routes))
            $this->routes[$offset] = [];

        array_push($this->routes[$offset], $route);
    }
}
