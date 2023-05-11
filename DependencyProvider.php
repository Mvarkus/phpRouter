<?php

namespace MVarkus\Routing;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use InvalidArgumentException;

class DependencyProvider
{
    /**
     * @var array
     */
    private array $dependencyDictionary = [];

    /**
     * @var array
     */
    private array $singletons = [];

    /**
     * @param array $dependencies
     * @throws \InvalidArgumentException
     */
    public function __construct(array $dependencies)
    {
        foreach ($dependencies as $dependency) {
            if (!$dependency instanceof Dependency) {
                throw new InvalidArgumentException('$dependencies must be an array of ' . Dependency::class . ' instances');
            }

            $this->dependencyDictionary[$dependency->getAbstract()] = $dependency;
        }
    }

    /**
     * @param string $class
     * @return array
     */
    public function getConstructorDependencies(string $class): array
    {
        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return [];
        }

        $parameters = $constructor->getParameters();

        if (empty($parameters)) {
            return [];
        }

        return $this->getDependencies($parameters);
    }

    /**
     * @param object $instance
     * @param string $method
     * @return array
     */
    public function getMethodDependencies($instance, string $method): array
    {
        $reflection = new ReflectionMethod($instance, $method);

        $parameters = $reflection->getParameters();

        if (empty($parameters)) {
            return [];
        }

        return $this->getDependencies($parameters);
    }

    /**
     * @param \Closure $closure
     * @return array
     */
    public function getFunctionDependencies(Closure $closure): array
    {
        $reflection = new ReflectionFunction($closure);

        $parameters = $reflection->getParameters();

        if (empty($parameters)) {
            return [];
        }

        return $this->getDependencies($parameters);
    }

    /**
     * @param array $parameters
     * @return array
     */
    public function getDependencies(array $parameters): array
    {
        $dependencies = [];
        $skipable = ['string', 'bool', 'int', 'float', 'array', 'callable', 'resource', Closure::class];

        foreach ($parameters as $parameter) {
            $abstract = $parameter->getType()->getName();

            if (!in_array($abstract, $skipable) && !is_callable($abstract)) {

                if (array_key_exists($abstract, $this->dependencyDictionary)) {
                    $routeDependencyType = $this->dependencyDictionary[$abstract]->getType();

                    if ($routeDependencyType == DependencyType::Singleton) {
                        if (!array_key_exists($abstract, $this->singletons)) {
                            $this->singletons[$abstract] = $this->createDependencyConcrete(
                                $this->dependencyDictionary[$abstract]->getConcrete()
                            );
                        }

                        $dependencies[] = $this->singletons[$abstract];
                    } elseif ($routeDependencyType == DependencyType::Transient) {
                        $dependencies[] = $this->createDependencyConcrete(
                            $this->dependencyDictionary[$abstract]->getConcrete()
                        );
                    }
                } else {
                    $dependencies[] = new $abstract(...$this->getConstructorDependencies($abstract));
                }
            }
        }

        return $dependencies;
    }

    /**
     * @param string|\Closure $dependencyConcrete
     * @return void
     */
    private function createDependencyConcrete($dependencyConcrete)
    {
        return is_string($dependencyConcrete)
            ? new $dependencyConcrete(
                ...$this->getConstructorDependencies($dependencyConcrete)
            )
            : $dependencyConcrete(
                ...$this->getFunctionDependencies($dependencyConcrete)
            );
    }
}
