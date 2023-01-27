<?php

namespace IDT\LaravelCommon\Lib\Traits;


use Illuminate\Http\Resources\Json\{AnonymousResourceCollection, JsonResource};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;

trait CastsToResource
{
    public function resolveResourceClass(string $resourceClass = null): string
    {
        return $resourceClass ?? $this->usesResourceClass();
    }

    public function asResource(string $resourceClass = null): JsonResource
    {
        $resourceClass = $this->resolveResourceClass($resourceClass);

        return new $resourceClass($this);
    }

    /**
     * @param Builder           $builder
     * @param class-string|null $resourceClass
     * @param string|null       $path
     *
     * @return AnonymousResourceCollection
     */
    public function scopeAsResourceCollection(
        Builder $builder,
        string $resourceClass = null,
        string $path = null
    ): AnonymousResourceCollection {
        $resourceClass = $this->resolveResourceClass($resourceClass);

        /** @var AbstractPaginator */
        $paginator = $builder->paginate(25);

        return $resourceClass::collection($paginator->withPath($path));
    }

    public function scopeAsResourceList(
        Builder $builder,
        string $resourceClass = null
    ): AnonymousResourceCollection {
        $resourceClass = $this->resolveResourceClass($resourceClass);

        return $resourceClass::collection($builder->get());
    }
}
