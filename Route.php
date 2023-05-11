<?php

namespace MVarkus\Routing;

use Closure;
use InvalidArgumentException;

class Route
{
    /**
     * @var string
     */
    private string $method;

    /**
     * @var string
     */
    private string $pathPattern;

    /**
     * @var array|Closure
     */
    private $action;

    /**
     * @var array
     */
    private array $defaultParameters;

    /**
     * @param string $method
     * @param string $pathPattern
     * @param array|\Closure $action
     * @param array $defaultParameters
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $method,
        string $pathPattern,
        $action,
        array $defaultParameters
    ) {
        $this->method = $method;
        $this->pathPattern = $pathPattern;

        if (!($action instanceof Closure || (is_array($action) && count($action) === 2))) {
            throw new InvalidArgumentException(
                "Route action must be callable or array with controller and action"
            );
        }

        $this->action = $action;
        $this->defaultParameters = $defaultParameters;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPathPattern(): string
    {
        return $this->pathPattern;
    }

    /**
     * @return array|\Closure
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return $this->defaultParameters;
    }
}
