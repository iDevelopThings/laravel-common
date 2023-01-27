<?php

namespace IDT\LaravelCommon\Lib\HashIds;

use Illuminate\Database\Eloquent\Model;
use \Vinkla\Hashids\Facades\Hashids as HashidsFacade;
use Vinkla\Hashids\HashidsFactory;

class HashIds
{
    /**
     * @var Model|mixed
     */
    private Model $modelInst;

    private int|string|null $id = null;

    /**
     * @param class-string|Model $model
     */
    public function __construct(string|Model $model, int|null $id = null)
    {
        if (!is_a($model, Model::class, true)) {
            throw new \InvalidArgumentException("Model must be a subclass of Illuminate\Database\Eloquent\Model");
        }

        $this->modelInst = is_string($model) ? new $model() : $model;
        $this->id        = $id ?? (is_string($model) ? null : $model->getKey());
    }


    /**
     * @param string|Model $model
     * @param int|null     $id
     *
     * @return HashIds
     */
    public static function forModel(string|Model $model, int|null $id = null): HashIds
    {
        return new HashIds($model, $id);
    }

    /**
     * @param string $hashId
     *
     * @return array{0: string, 1: string}
     */
    public static function decode(string $hashId): array
    {
        $str          = str($hashId);
        $prefix       = null;
        $hashIdStr    = null;
        $hashIdResult = null;

        if ($str->contains('_')) {
            $prefix    = $str->beforeLast('_')->toString();
            $hashIdStr = $str->afterLast('_')->toString();
        } else {
            $hashIdStr = $hashId;
        }

        $hashIdResult = HashidsFacade::decode($hashIdStr);
        if (is_array($hashIdResult) && count($hashIdResult) === 1) {
            $hashIdResult = $hashIdResult[0];
        }

        return [$hashIdResult, $prefix];
    }

    public function getPrefix(): string
    {
        return self::formatPrefix($this->modelInst->getTable());
    }

    public function get(int|string|null $id = null): string
    {
        return self::formatHashId($this->getPrefix(), $id ?? $this->id);
    }

    public static function encode(int $id): string
    {
        return HashidsFacade::encode($id);
    }

    public static function formatPrefix(string $prefix): string
    {
        return str($prefix)->singular()->toString();
    }

    public static function formatHashId(string $prefix, int $id): string
    {
        return $prefix . '_' . self::encode($id);
    }

}
