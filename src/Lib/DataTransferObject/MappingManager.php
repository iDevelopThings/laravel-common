<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject;

use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResolver;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResult;
use IDT\LaravelCommon\Lib\Utils\Reflection\ReflectionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use Throwable;

class MappingManager
{

    /** @var array<class-string, MappingManager> */
    public static array $cache = [];

    public ReflectionClass $reflectionClass;

    /** @var Collection<int, ReflectionProperty> */
    public Collection $properties;

    /** @var Collection<int, ReflectionMethod> */
    public Collection $methods;

    public static function for(string|DataTransferObject|ReflectionClass $class): static
    {
        if ($class instanceof ReflectionClass) {
            $class = $class->getName();
        } elseif (!is_string($class)) {
            $class = get_class($class);
        }

        if (self::$cache[$class] ?? false) {
            return self::$cache[$class];
        }

        $instance                  = new static();
        $instance->reflectionClass = new ReflectionClass($class);
        $instance->properties      = collect($instance->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC));
        $instance->methods         = collect($instance->reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC));

        return self::$cache[$class] = $instance;
    }

    public function createNewInstance()
    {
        return $this->reflectionClass->newInstance();
    }

    /**
     * Sets all request data properties onto the DTO
     *
     * If the property is mappable, it will be mapped to the correct type
     * For example, a property with a DTO type, will be mapped
     * A string value for an Enum property will be mapped to the correct Enum... etc
     *
     * @param Request    $request
     * @param bool       $isRootDto
     * @param mixed|null $instance
     *
     * @return mixed
     * @throws Throwable
     * @throws \ReflectionException
     */
    public function fillWithRequestData(Request $request, bool $isRootDto = true, mixed $instance = null)
    {
        $resolver = resolve(MappingResolver::class);

        /** @var DataTransferObject $instance */
        $instance = $instance ?? $this->createNewInstance();
        $data     = $request->all();

        $this->reflectionClass->getParentClass()->getProperty('_rawData')->setValue($instance, $data);

        if ($isRootDto) {
            $this->addNestedValidationRules($instance);
            $instance->validate();
        }

        if (method_exists($instance, 'beforeFilling')) {
            call_user_func([$instance, 'beforeFilling']);
        }

        foreach ($this->properties as $property) {
            $propertyValue        = null;
            $requestPropertyValue = $data[$property->getName()] ?? null;

            // $propertyType = $property->getType();
            // if (!$propertyType) {
            //     $property->setValue($this, $requestPropertyValue);
            //     continue;
            // }

            $types = array_filter(ReflectionUtil::getPropertyTypes($property));
            if (empty($types)) {
                $rProp = ReflectionUtil::getType($property);
                $types = array_filter([$rProp->resolveTypeToPhpType()]);
            }
            if (empty($types)) {
                continue;
            }
            foreach ($types as $type) {

                $result = $this->mapValue(
                    $resolver,
                    $instance,
                    $request,
                    $type,
                    $property,
                    $requestPropertyValue
                );

                if ($result->failed()) {
                    throw $result->getException();
                }

                if ($result->didResolve()) {
                    $propertyValue = $result->getValue();
                    break;
                }

            }

            $property->setValue($instance, $propertyValue);
        }

        if (method_exists($instance, 'afterFilling')) {
            call_user_func([$instance, 'afterFilling']);
        }

        return $instance;
    }

    public function mapValue(
        MappingResolver $resolver,
        DataTransferObject $dto,
        ?Request $request,
        ReflectionNamedType|ReflectionClass $type,
        ReflectionProperty $property,
        mixed $value
    ): MappingResult {
        try {
            return $resolver->runFqnMapper($dto, $this, $type, $property, $request, $value);
        } catch (Throwable $ex) {
            return MappingResult::failure($ex);
        }
    }

    protected function getValidationRules(DataTransferObject $dto): array
    {
        $rules = $dto->getValidator()->getRules();
        if (method_exists($dto, 'rules')) {
            $rules = array_merge($rules, $dto->rules());
        }

        return $rules;
    }

    public function resolveAllValidators(?DataTransferObject $dto, string $parentValidationPath = ''): array
    {
        $dto ??= $this->createNewInstance();

        $validationRules = $this->getValidationRules($dto);

        foreach ($this->properties as $property) {
            $types = array_filter(ReflectionUtil::getPropertyTypes($property));
            if (empty($types)) {
                $rProp = ReflectionUtil::getType($property);
                $types = array_filter([$rProp->resolveTypeToPhpType()]);
            }
            if (empty($types)) {
                continue;
            }
            foreach ($types as $type) {
                $fqn = $type?->getName();

                // if (is_a($fqn, DataTransferObjectCollection::class)) {
                //     throw new Exception('implement pls');
                // }

                if (is_a($fqn, DataTransferObject::class, true)) {
                    $mapper = MappingManager::for($fqn);

                    $path = $parentValidationPath . $property->getName() . '.';

                    $rules = $mapper->resolveAllValidators($mapper->createNewInstance(), $path);

                    foreach ($rules as $key => $value) {
                        $validationRules[$path . $key] = $value;
                    }
                }


            }
        }

        return $validationRules;
    }

    private function addNestedValidationRules(DataTransferObject $instance)
    {
        $rules = $this->resolveAllValidators($instance);

        if (!empty($rules)) {
            $instance->getValidator()->addRules($rules);
        }
    }

}
