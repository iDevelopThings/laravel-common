<?php


it('app gets mixed in correctly', function () {
    class TestClass
    {
        public function __construct(public $value)
        {
        }
    }

    app()->bind(TestClass::class);

    $result = app()->resolveWith(TestClass::class, ['Hello World']);

    expect($result)->toBeInstanceOf(TestClass::class);
    expect($result->value)->toBe('Hello World');

});
