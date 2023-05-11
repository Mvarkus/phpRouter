<?php

namespace MVarkus\Routing;

class RouteConfigurationStack
{
    /**
     * @var array
     */
    private array $routeConfigurations = [];

    /**
     * @param \MVarkus\Routing\RouteConfiguration $routeConfiguration
     * @return void
     */
    public function push(RouteConfiguration $routeConfiguration): void
    {
        array_push($this->routeConfigurations, $routeConfiguration);
    }

    /**
     * @return \MVarkus\Routing\RouteConfiguration
     */
    public function pop(): RouteConfiguration
    {
        return array_pop($this->routeConfigurations);
    }

    /**
     * @return integer
     */
    public function count(): int
    {
        return count($this->routeConfigurations);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return array_reduce(
            $this->routeConfigurations,
            function (string $carry, RouteConfiguration $item) {
                $carry = $carry . $item->getPathPatternPrefix();

                return $carry;
            },
            ''
        );
    }

    /**
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return array_reduce(
            $this->routeConfigurations,
            function (array $carry, RouteConfiguration $item) {
                return array_merge($carry, $item->getDefaultParameters());
            },
            []
        );
    }

    /**
     * @return array
     */
    public function getPathConstraints(): array
    {
        return array_reduce(
            $this->routeConfigurations,
            function (array $carry, RouteConfiguration $item) {
                return array_merge($carry, $item->getPathConstraints());
            },
            []
        );
    }
}
