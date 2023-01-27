<?php

namespace IDT\LaravelCommon\Lib\ApiResponse;



use IDT\LaravelCommon\Lib\Utils\Enums\HttpStatusCode;

class SuccessResponse extends ApiResponse
{
    protected int $code = 200;

    public function __construct()
    {
        $this->data['success'] = true;
    }

    public static function noContent(): SuccessResponse
    {
        return (new static())->statusCode(HttpStatusCode::No_Content);
    }

    /**
     * @param string|null        $message
     * @param int|HttpStatusCode $code
     *
     * @return SuccessResponse
     */
    public static function create(?string $message = null, HttpStatusCode|int $code = 200): SuccessResponse
    {
        $response = new static();
        if ($message) {
            $response->data['message'] = $message;
        }
        $response->statusCode($code);

        return $response;
    }


}
