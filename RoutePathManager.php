<?php

namespace MVarkus\Routing;

class RoutePathManager
{
    /**
     * @param string $rawPathPattern
     * @param array $pathConstraints
     * @return string
     */
    public function createPathPattern(string $rawPathPattern, array $pathConstraints): string
    {
        $placeholders = [];
        $replacements = [];
        $pathPattern = preg_replace('~/({[0-9a-zA-Z]+\?})~', '$1', $rawPathPattern);

        foreach ($pathConstraints as $key => $value) {
            if (strpos($pathPattern, "{" . $key . "?}")) {
                $placeholders[] = "~{" . $key . "\?}~";
                $replacements[] = "(/(?P<$key>$value))?";
            } else {
                $placeholders[] = "~{" . $key . "}~";
                $replacements[] = "(?P<$key>$value)";
            }
        }

        if (count($placeholders) !== null)
            $pathPattern = preg_replace($placeholders, $replacements, $pathPattern);

        return "~^$pathPattern$~";
    }

    /**
     * @param string $path
     * @param string $pathPattern
     * @return boolean
     */
    public function match(string $path, string $pathPattern): bool
    {
        return preg_match($pathPattern, $path, $matches) === 1;
    }

    /**
     * @param string $path
     * @param string $pathPattern
     * @return array
     */
    public function extractPathParameters(string $path, string $pathPattern): array
    {
        $parameters = [];

        preg_match($pathPattern, $path, $matches);

        foreach ($matches as $key => $parameter) {
            if (!is_int($key) && $matches[$key] !== '') {
                $parameters[$key] = $parameter;
            }
        }

        return $parameters;
    }
}
