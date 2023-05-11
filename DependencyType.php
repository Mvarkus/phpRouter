<?php

namespace MVarkus\Routing;

abstract class DependencyType
{
    /**
     * @var string
     */
    const Singleton = 'singleton';

    /**
     * @var string
     */
    const Transient = 'transient';

    /**
     * @param string $value
     * @return boolean
     */
    public static function isDefined(string $value): bool
    {
        return self::Singleton === $value
            || self::Transient === $value;
    }
}
