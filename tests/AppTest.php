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


it('calling method with correct injected params', function () {
    class TestClassMethodInjection
    {
        public function testMethod(string $strValue, Illuminate\Config\Repository $config)
        {
            if ($strValue !== 'Hello World') {
                return false;
            }

            return $config;
        }
    }

    $result = app()->callWith([new TestClassMethodInjection, 'testMethod'], ['Hello World']);

    expect($result)->toBeInstanceOf(Illuminate\Config\Repository::class);
});
