<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mappers;


use Exception;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\DtoMappingHandler;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResolver;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\MappingResult;
use IDT\LaravelCommon\Lib\DataTransferObject\Mapping\TestableMapper;
use IDT\LaravelCommon\Lib\DataTransferObject\MappingManager;
use IDT\LaravelCommon\Lib\Utils\Reflection\Annotations\TypeFromAnnotation;
use IDT\LaravelCommon\Lib\Utils\Reflection\ReflectionUtil;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;

class ArrayableMapper extends DtoMappingHandler implements TestableMapper
{
    public function handle(mixed $data): MappingResult
    {
        if (!in_array($this->type->getName(), ['array', 'Illuminate\Support\Collection'])) {
            return $this->failure(new Exception('ArrayableMapper can only map to array'));
        }

        $type  = ReflectionUtil::getType($this->property);
        $annot = $type->resolveType();

        /** @var MappingResolver $resolver */
        $mappingResolver = resolve(MappingResolver::class);

        if ($annot) {
            /** @var DtoMappingHandler $resolver */
            $resolver = $this->tryGetResolver($annot, $mappingResolver);

            if ($resolver) {
                $mapped = collect($data)
                    ->map(function ($item) use ($resolver) {
                        $result = $resolver->handle($item);
                        if ($result->didResolve()) {
                            return $result->getValue();
                        }

                        return null;
                    })
                    ->filter(fn($item) => !is_null($item));

                if ($annot->fqn === '\Illuminate\Support\Collection') {
                    return $this->success($mapped->values());
                }

                return $this->success($mapped->all());
            }

            if ($annot->isArrayType()) {
                return $this->success(
                    Arr::map($data, fn($item) => $mappingResolver->tryMapBuiltinValue($annot->typeName, $item))
                );
            }
        }

        return $this->success((array)$data);
    }

    public static function canMap(ReflectionProperty $property, ReflectionNamedType|ReflectionClass $type): array
    {
        if ($type->getName() === 'array') {
            return [true, null];
        }

        return [false, null];
    }

    private function tryGetResolver(?TypeFromAnnotation $annot, MappingResolver $resolver)
    {
        if ($annot->hasFqnValueType()) {
            $newType = new ReflectionClass($annot->valueType);
            $mapper  = $resolver->findFqnMapper(
                $newType,
                $this->property
            );

            return new $mapper(
                $this->dto,
                $this->mappingManager,
                $newType,
                $this->property,
                $this->requestData,
            );
        }

        return null;
    }

}
