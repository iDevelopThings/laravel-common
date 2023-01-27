<?php

namespace IDT\LaravelCommon\Lib\DataTransferObject\Mapping;

use Throwable;

class MappingResult
{
    protected bool $didResolve = false;

    protected mixed $value = null;

    protected ?Throwable $exception = null;

    public static function success(mixed $value): self
    {
        $result             = new self();
        $result->didResolve = true;
        $result->value      = $value;

        return $result;
    }

    public static function noResult(): self
    {
        $result             = new self();
        $result->didResolve = true;
        $result->value      = null;

        return $result;
    }

    public static function failure(Throwable $exception): self
    {
        $result             = new self();
        $result->didResolve = false;
        $result->exception  = $exception;

        return $result;
    }

    public function didResolve(): bool
    {
        return $this->didResolve;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    public function failed()
    {
        return !$this->didResolve && $this->exception !== null;
    }


}
