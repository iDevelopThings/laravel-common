<?php

namespace IDT\LaravelCommon;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Reflector;
use Laravel\SerializableClosure\Support\ReflectionClosure;
use ReflectionClass;

/** @mixin Application */
class AppExtension
{

    /**
     * @template T of class-string
     *
     * @param string|T $abstract
     * @param array    $parameters
     *
     * @return T
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

}
