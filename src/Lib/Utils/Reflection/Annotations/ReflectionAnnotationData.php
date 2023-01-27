<?php

namespace IDT\LaravelCommon\Lib\Utils\Reflection\Annotations;

class ReflectionAnnotationData
{
    public function __construct(
        /**
         * @var string|null $annotationType | For ex, @return, @var, @param etc
         */
        public string|null $annotationType,
        /**
         * @var string|null $type | The type in the annotation, for ``@return string``, this would be "string"
         */
        public string|null $type,
        /**
         * @var string|null $var | The variable name in the annotation, for ``@param string $var``, this would be "$var"
         */
        public string|null $var,
        /**
         * @var string|null $message | The message in the annotation, for ``@param string $var This is a message``, this would be "This is a message"
         */
        public string|null $message,
    ) {

    }
}
