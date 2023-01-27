<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use InvalidArgumentException;
use JsonSerializable;

abstract class DataTransferObject implements Castable, Arrayable, JsonSerializable
{
    private array $_rawData = [];

    protected Validator $validator;

    /**
     * @param array $attributes
     * @param bool  $partial
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $attributes = [], bool $partial = true)
    {
        $this->validator = ValidatorFacade::make([], []);

        $this->fill($attributes, $partial);
    }

    /**
     * @param array $attributes
     * @param bool  $partial
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function fill(array $attributes, bool $partial = true): static
    {
        $mapping = MappingManager::for($this);

        $missingAttributes = [];

        foreach ($mapping->properties as $property) {
            if (!array_key_exists($property->getName(), $attributes)) {
                $missingAttributes[] = $property->getName();
                continue;
            }

            $value = $attributes[$property->getName()];

            $propertyType = (string)$property->getType();

            if (class_exists($propertyType)) {
                $value = new $propertyType($value);
            }

            $this->{$property->getName()} = $value;
        }

        if (!$partial && count($missingAttributes)) {
            $missing = implode(', ', $missingAttributes);
            throw new InvalidArgumentException('The payload for ' . static::class . ' is missing the following attributes: ' . $missing);
        }

        return $this;
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function validate(mixed $data = null)
    {
        $data = $data ?? $this->_rawData;

        $this->validator->setData($data);
        $this->validator->validate();
    }

    public static function castUsing(array $arguments): DataTransferObjectCast
    {
        return new DataTransferObjectCast(static::class);
    }

    public function toArray(array $overrides = []): array
    {
        $mapping = MappingManager::for($this);

        $attributes = [];

        foreach ($mapping->properties as $property) {
            if (!$property->isInitialized($this) || !$property->isDefault()) {
                continue;
            }

            $value = $property->getValue($this);

            $attributes[$property->getName()] = $value instanceof Arrayable ? $value->toArray() : $value;
        }

        return array_merge($attributes, $overrides);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
