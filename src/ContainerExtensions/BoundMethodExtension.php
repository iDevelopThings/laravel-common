<?php

namespace IDT\LaravelCommon\ContainerExtensions;

use Illuminate\Container\BoundMethod;

class BoundMethodExtension extends BoundMethod
{
    /**
     * Get all dependencies for a given method.
     *
     * @param \Illuminate\Container\Container $container
     * @param callable|string                 $callback
     * @param array                           $parameters
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    protected static function getMergedMethodDependencies($container, $callback, array $parameters = [])
    {
        $makeArgs     = [];
        $dependencies = [];

        $reflectionParams = static::getCallReflector($callback)->getParameters();

        for ($i = 0; $i < count($reflectionParams); $i++) {
            if (isset($parameters[$i])) {
                $makeArgs[$reflectionParams[$i]->getName()] = $parameters[$i];
            }
        }

        foreach ($reflectionParams as $parameter) {
            static::addDependencyForCallParameter($container, $parameter, $makeArgs, $dependencies);
        }

        return array_merge($dependencies, array_values($makeArgs));
    }

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param \Illuminate\Container\Container $container
     * @param callable|string                 $callback
     * @param array                           $parameters
     * @param string|null                     $defaultMethod
     *
     * @return mixed
     *
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public static function callWith($container, $callback, array $parameters = [], $defaultMethod = null)
    {
        if (is_string($callback) && !$defaultMethod && method_exists($callback, '__invoke')) {
            $defaultMethod = '__invoke';
        }

        if (static::isCallableWithAtSign($callback) || $defaultMethod) {
            return static::callClass($container, $callback, $parameters, $defaultMethod);
        }

        return static::callBoundMethod($container, $callback, function () use ($container, $callback, $parameters) {
            $dependencies = static::getMergedMethodDependencies($container, $callback, $parameters);

            return $callback(...array_values($dependencies));
        });
    }

}
