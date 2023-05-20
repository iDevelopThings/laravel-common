<?php


use IDT\LaravelCommon\Lib\DataTransferObject\DataTransferObject;
use IDT\LaravelCommon\Tests\Fixtures\Dto\BasicDto;
use IDT\LaravelCommon\Tests\Fixtures\Dto\ComplexDto;
use Illuminate\Http\Request;

$complexData = [
    'basicDto'           => [
        'intValue'    => 1,
        'floatValue'  => 1.1,
        'boolValue'   => true,
        'stringValue' => 'string',
        'arrayValue'  => [1, 2, 3],
    ],
    'basicDtoArray'      => [
        [
            'intValue'    => 1,
            'floatValue'  => 1.1,
            'boolValue'   => true,
            'stringValue' => 'string',
            'arrayValue'  => [1, 2, 3],
        ],
        [
            'intValue'    => 1,
            'floatValue'  => 1.1,
            'boolValue'   => true,
            'stringValue' => 'string',
            'arrayValue'  => [1, 2, 3],
        ],
    ],
    'basicDtoCollection' => [
        [
            'intValue'    => 1,
            'floatValue'  => 1.1,
            'boolValue'   => true,
            'stringValue' => 'string',
            'arrayValue'  => [1, 2, 3],
        ],
        [
            'intValue'    => 1,
            'floatValue'  => 1.1,
            'boolValue'   => true,
            'stringValue' => 'string',
            'arrayValue'  => [1, 2, 3],
        ],
    ],
];

it('can use the fill method to fill the dto', function () {
    enum SomeNormalEnum
    {
        case A;
        case B;
    }

    enum SomeBackedEnum: string
    {
        case A = 'A';
        case B = 'B';
    }

    $dto = new class() extends DataTransferObject {
        public ?int            $intValue       = null;
        public ?float          $floatValue     = null;
        public ?bool           $boolValue      = null;
        public ?string         $stringValue    = null;
        public ?array          $arrayValue     = null;
        public ?SomeNormalEnum $someNormalEnum = null;
        public ?SomeBackedEnum $someBackedEnum = null;
    };
    $dto->fill([
        'intValue'       => 1,
        'floatValue'     => 1.1,
        'boolValue'      => true,
        'stringValue'    => 'string',
        'arrayValue'     => [1, 2, 3],
        'someNormalEnum' => 0,
        'someBackedEnum' => 'B',
    ]);

    expect($dto->intValue)->toBe(1)
        ->and($dto->floatValue)->toBe(1.1)
        ->and($dto->boolValue)->toBeTrue()
        ->and($dto->stringValue)->toBe('string')
        ->and($dto->arrayValue)->toBe([1, 2, 3]);
});


it('can fill the dto with basic data from the request, using the container', function () {
    $request = new Request([], [], ['info' => 5]);

    $request->merge([
        'intValue'    => 1,
        'floatValue'  => 1.1,
        'boolValue'   => true,
        'stringValue' => 'string',
        'arrayValue'  => [1, 2, 3],
    ]);

    app()->instance('request', $request);

    $dto = $this->app->make(BasicDto::class);

    expect($dto->intValue)->toBe(1)
        ->and($dto->floatValue)->toBe(1.1)
        ->and($dto->boolValue)->toBeTrue()
        ->and($dto->stringValue)->toBe('string')
        ->and($dto->arrayValue)->toBe([1, 2, 3]);
});

it('can fill the dto with more complex data from the request, using the container', function () use ($complexData) {
    $request = new Request([], [], ['info' => 5]);

    $request->merge($complexData);

    app()->instance('request', $request);

    $dto = $this->app->make(ComplexDto::class);

    expect($dto->basicDto)->toBeInstanceOf(BasicDto::class)
        ->and($dto->basicDtoArray[0])->toBeInstanceOf(BasicDto::class)
        ->and($dto->basicDtoArray[1])->toBeInstanceOf(BasicDto::class)
        ->and($dto->basicDto->intValue)->toBe(1)
        ->and($dto->basicDto->floatValue)->toBe(1.1)
        ->and($dto->basicDto->boolValue)->toBeTrue()
        ->and($dto->basicDto->stringValue)->toBe('string')
        ->and($dto->basicDto->arrayValue)->toBe([1, 2, 3])
        ->and($dto->basicDtoArray[0]->intValue)->toBe(1)
        ->and($dto->basicDtoArray[0]->floatValue)->toBe(1.1)
        ->and($dto->basicDtoArray[0]->boolValue)->toBeTrue()
        ->and($dto->basicDtoArray[0]->stringValue)->toBe('string')
        ->and($dto->basicDtoArray[0]->arrayValue)->toBe([1, 2, 3]);
});

it('can fill the dto with more complex data from using the constructor/fill method', function () use ($complexData) {

    $dto = new ComplexDto($complexData);

    expect($dto->basicDto)->toBeInstanceOf(BasicDto::class)
        ->and($dto->basicDtoArray[0])->toBeInstanceOf(BasicDto::class)
        ->and($dto->basicDtoArray[1])->toBeInstanceOf(BasicDto::class)
        ->and($dto->basicDto->intValue)->toBe(1)
        ->and($dto->basicDto->floatValue)->toBe(1.1)
        ->and($dto->basicDto->boolValue)->toBeTrue()
        ->and($dto->basicDto->stringValue)->toBe('string')
        ->and($dto->basicDto->arrayValue)->toBe([1, 2, 3])
        ->and($dto->basicDtoArray[0]->intValue)->toBe(1)
        ->and($dto->basicDtoArray[0]->floatValue)->toBe(1.1)
        ->and($dto->basicDtoArray[0]->boolValue)->toBeTrue()
        ->and($dto->basicDtoArray[0]->stringValue)->toBe('string')
        ->and($dto->basicDtoArray[0]->arrayValue)->toBe([1, 2, 3]);
});
