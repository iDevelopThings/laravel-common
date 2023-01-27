<?php

// config for IDT/LaravelCommon
return [

    'dto' => [
        /*
        |--------------------------------------------------------------------------
        | Allow resolving from container
        |--------------------------------------------------------------------------
        | This will attempt to resolve any class types from the container
        | It makes the assumption that the class will handle the value in the constructor
        */
        'allowResolvingFromContainer' => true,

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
