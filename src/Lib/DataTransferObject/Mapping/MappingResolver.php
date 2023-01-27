<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mapping;


use App;
use Arr;
use BackedEnum;
use Exception;
use IDT\LaravelCommon\Lib\DataTransferObject\DataTransferObject;
use IDT\LaravelCommon\Lib\DataTransferObject\Mappers\ArrayableMapper;
use IDT\LaravelCommon\Lib\DataTransferObject\Mappers\BackedEnumMapper;
use IDT\LaravelCommon\Lib\DataTransferObject\Mappers\Builtin\BoolMapper;
use IDT\LaravelCommon\Lib\DataTransferObject\Mappers\Builtin\FloatMapper;
use IDT\LaravelCommon\Lib\DataTransferObject\Mappers\Builtin\IntMapper;
use IDT\LaravelCommon\Lib\DataTransferObject\Mappers\Builtin\StringMapper;
use IDT\LaravelCommon\Lib\DataTransferObject\Mappers\DtoMapper;
use IDT\LaravelCommon\Lib\DataTransferObject\Mappers\ModelMapper;
use IDT\LaravelCommon\Lib\DataTransferObject\MappingManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Throwable;

class MappingResolver
{

    /** @var array<string, TestableMapper> */
    public array $builtInResolvers;

    /** @var array<class-string, DtoMappingHandler> */
    public array $resolvers;

    /** @var array<TestableMapper> */
    public array $testableResolvers;


    public function __construct(array $config)
    {

        $this->builtInResolvers = [
            'bool'   => BoolMapper::class,
            'float'  => FloatMapper::class,
            'int'    => IntMapper::class,
            'string' => StringMapper::class,
            'array'  => ArrayableMapper::class,
        ];

        $this->testableResolvers = array_merge(
            $this->builtInResolvers,
            ($config['testable'] ?? []),
        );


        $this->resolvers = [
            Collection::class         => ArrayableMapper::class,
            DataTransferObject::class => DtoMapper::class,
            BackedEnum::class         => BackedEnumMapper::class,
            Model::class              => ModelMapper::class,
        ];

        $this->resolvers = array_merge(
            $this->resolvers,
            ($config['resolvers'] ?? []),
        );
    }

    /**
     * @param ReflectionNamedType $type
     * @param ReflectionProperty  $property
     *
     * @return class-string|null
     */
    public function findFqnMapper(ReflectionNamedType|ReflectionClass $type, ReflectionProperty $property): ?string
    {
        foreach ($this->resolvers as $fqn => $resolverFqn) {
            if (!is_a($type->getName(), $fqn, true)) {
                continue;
            }

            return $resolverFqn;
        }

        foreach ($this->testableResolvers as $resolver) {
            [$canMap, $mapper] = $resolver::canMap($property, $type);
            if ($canMap) {
                return $mapper ?? $resolver;
            }
        }

        return null;
    }

    public function tryMapBuiltinValue(string $builtinTypeName, $value)
    {
        $resolver = $this->builtinResolvers[$builtinTypeName] ?? null;
        if (!$resolver) {
            return $value;
        }

        $resolver = new $resolver(
            dto: null,
            mappingManager: null,
            type: null,
            property: null,
            request: request() ?? null,
        );

        return $resolver->handle($value);
    }

    public function runFqnMapper(
        DataTransferObject $dto,
        MappingManager $mappingManager,
        ReflectionNamedType|ReflectionClass $type,
        ReflectionProperty $property,
        ?Request $request,
        mixed $value
    ): MappingResult {
        try {
            /** @var class-string<DtoMappingHandler> $resolverFqn */
            $resolverFqn = $this->findFqnMapper($type, $property);

            if (!$resolverFqn && config('laravel-common.dto.allowResolvingFromContainer', true)) {
                if (class_exists($type?->getName()) || interface_exists($type?->getName())) {
                    $result = App::resolveWith($type->getName(), [$value]);
                    if ($result) {
                        return MappingResult::success($result);
                    }
                }
            }

            if (!$resolverFqn) {
                return MappingResult::failure(new Exception('No resolver found for type ' . $type->getName()));
            }

            $resolver = new $resolverFqn(
                dto: $dto,
                mappingManager: $mappingManager,
                type: $type,
                property: $property,
                request: $request,
            );

            return $resolver->handle($value);
        } catch (Throwable $ex) {
            return MappingResult::failure($ex);
        }
    }

}
