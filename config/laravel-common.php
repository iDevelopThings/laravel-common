<?php

// config for IDT/LaravelCommon
return [

    'dto' => [
        'mappers' => [
            // Mappers implementing TestableMapper interface
            // @see \IDT\LaravelCommon\Lib\DataTransferObject\Mappers\Builtin\BoolMapper
            'testable'  => [
                // SomeTestableMapper::class
            ],

            // Regular mappers, runs "is_a(key, value, true)" to check if mapper can be used
            'resolvers' => [
                //ValueIWantToMap::class => SomeMapper::class
            ],
        ],


    ],

];
