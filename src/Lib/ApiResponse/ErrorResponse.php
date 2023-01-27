<?php

namespace IDT\LaravelCommon\Lib\ApiResponse;


use IDT\LaravelCommon\Lib\Utils\Enums\HttpStatusCode;

class ErrorResponse extends ApiResponse
{
	protected int $code = 400;

	public function __construct()
	{
		$this->data['success'] = false;
	}

	/**
	 * @param string             $error
	 * @param int|HttpStatusCode $statusCode
	 *
	 * @return ErrorResponse
	 */
	public static function create(string $error, HttpStatusCode|int $statusCode = 400): ErrorResponse
	{
		$response                  = new static();
		$response->data['message'] = $error;

		$response->statusCode($statusCode);

		return $response;
	}


}
