<?php

namespace MVarkus\Routing;

class RouteConfiguration
{
    /**
     * @var string
     */
    private string $pathPatternPrefix;

    /**
     * @var array
     */
    private array $defaultParameters;

    /**
     * @var array
     */
    private array $pathConstraints;

    /**
     * @param string $pathPatternPrefix
     * @param array $defaultParameters
     * @param array $pathConstraints
     */
    public function __construct(
        string $pathPatternPrefix,
        array $defaultParameters,
        array $pathConstraints
    ) {
        $this->pathPatternPrefix = $pathPatternPrefix;
        $this->defaultParameters = $defaultParameters;
        $this->pathConstraints = $pathConstraints;
    }

    /**
     * @return string
     */
    public function getPathPatternPrefix(): string
    {
        return $this->pathPatternPrefix;
    }

    /**
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return $this->defaultParameters;
    }

    /**
     * @return array
     */
    public function getPathConstraints(): array
    {
        return $this->pathConstraints;
    }
}
