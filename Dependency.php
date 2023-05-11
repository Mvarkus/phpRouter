<?php

namespace MVarkus\Routing;

use Closure;
use InvalidArgumentException;

class Dependency
{
    /**
     * @var string
     */
    private string $abstract;

    /**
     * @var string|\Closure
     */
    private $concrete;

    /**
     * @var string
     */
    private string $type;

    /**
     * @param string $abstract
     * @param string|\Closure $concrete
     * @param string $type (optional)
     */
    public function __construct(
        string $abstract,
        $concrete,
        string $type = DependencyType::Transient
    ) {
        $this->abstract = $abstract;

        if (!(is_string($concrete) || $concrete instanceof Closure))
            throw new InvalidArgumentException('Invalid type of $concrete argument');

        $this->concrete = $concrete;

        if (!DependencyType::isDefined($type))
            throw new InvalidArgumentException('$type');

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getAbstract(): string
    {
        return $this->abstract;
    }

    /**
     * @return string|Closure
     */
    public function getConcrete()
    {
        return $this->concrete;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
