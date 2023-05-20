<?php

namespace IDT\LaravelCommon;

use IDT\LaravelCommon\ContainerExtensions\BoundMethodExtension;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use ReflectionClass;

/** @mixin Application|Container */
class AppExtension
{

    /**
     * @template     T of class-string
     *
     * @param string|T $abstract
     * @param array    $parameters
     *
     * @return T
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpDocSignatureInspection
     */
    public function resolveWith()
    {
        return function ($abstract, array $parameters = []) {
            /** @var Application $this */

            $reflector   = new ReflectionClass($abstract);
            $constructor = $reflector->getConstructor();
            $makeArgs    = [];

            if ($constructor) {
                $i = 0;
                foreach ($constructor->getParameters() as $param) {
                    $pValue = $parameters[$i] ?? null;
                    if (!$pValue && $param->isDefaultValueAvailable()) {
                        $makeArgs[$param->getName()] = $param->getDefaultValue();
                        $i++;
                        continue;
                    }

                    $makeArgs[$param->getName()] = $pValue;
                    $i++;
                }
            }

            return app($abstract, $makeArgs);
        };
    }

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param callable|string      $callback
     * @param array<string, mixed> $parameters
     * @param string|null          $defaultMethod
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @noinspection PhpDocSignatureInspection
     */
    public function callWith()
    {
        return function ($callback, array $parameters = [], $defaultMethod = null) {
            /** @var Application|Container $this */

            $pushedToBuildStack = false;

            if (($className = $this->getClassForCallable($callback)) && ! in_array(
                    $className,
                    $this->buildStack,
                    true
                )) {
                $this->buildStack[] = $className;

                $pushedToBuildStack = true;
            }

            $result = BoundMethodExtension::callWith($this, $callback, $parameters, $defaultMethod);

            if ($pushedToBuildStack) {
                array_pop($this->buildStack);
            }

            return $result;
        };
    }

}
