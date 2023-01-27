<?php

use IDT\LaravelCommon\Lib\HashIds\HashIds;
use IDT\LaravelCommon\Tests\Fixtures\Models\TestingModel;

it('can encode', function () {
    $result = HashIds::encode(1);
    expect($result)->toBe('0zq29mvqdwrj5pg1');
});

it('can encode with prefix', function () {
    $result = HashIds::forModel(TestingModel::class, 1)->get();
    expect($result)->toBe('testing_model_0zq29mvqdwrj5pg1');
});


it('can decode', function () {
    $result = HashIds::decode('0zq29mvqdwrj5pg1');
    expect($result[0])->toBe(1)
        ->and($result[1])->toBe(null);
});

it('can decode with prefix', function () {
    $result = HashIds::decode('testing_model_0zq29mvqdwrj5pg1');
    expect($result[0])->toBe(1)
        ->and($result[1])->toBe('testing_model');
});


it('can use hash_id attribute to get hash id', function () {
    $model     = new TestingModel();
    $model->id = 1;
    expect($model->hash_id)->toBe('testing_model_0zq29mvqdwrj5pg1');
});
