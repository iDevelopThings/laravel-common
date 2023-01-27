<?php

namespace IDT\LaravelCommon\Lib\HashIds;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @mixin Model
 */
trait HasHashId
{
    public function hashId(): Attribute
    {
        return Attribute::make(
            get: fn() => HashIds::formatHashId($this->getHashIdPrefix(), $this->getKey()),
        )->shouldCache();
    }

    public function getHashIdPrefix(): string
    {
        return HashIds::formatPrefix($this->getTable());
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed       $value
     * @param string|null $field
     *
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        return $this->findByHashId($value);
    }

    /**
     * Get the value of the model's route key.
     *
     * @return string
     */
    public function getRouteKey(): string
    {
        return $this->hash_id;
    }

    public function findByHashId(string $hashId)
    {
        [$value, $prefix] = HashIds::decode($hashId);

        if ($prefix !== $this->getHashIdPrefix()) {
            return null;
        }

        return self::query()->where($this->getKeyName(), $value)->first();
    }

    public static function findOrFailByHashId(string $hashId)
    {
        $modelInst = resolve(static::class);

        return $modelInst->findByHashId($hashId) ?? throw new ModelNotFoundException();
    }
}
