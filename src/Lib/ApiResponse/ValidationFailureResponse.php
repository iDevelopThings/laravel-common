<?php

namespace IDT\LaravelCommon\Lib\ApiResponse;


use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidationFailureResponse extends ApiResponse
{
	protected int        $code      = 422;
	protected ?Validator $validator = null;

	public function __construct()
	{
		$this->data['success'] = false;
	}

	public static function create(Validator $validator): ValidationFailureResponse
	{
		$response            = new static();
		$response->validator = $validator;

		$response->statusCode(422);

		return $response;
	}

	public static function fromValidationException(ValidationException $exception): ValidationFailureResponse
	{
		return self::create($exception->validator)->statusCode($exception->status);
	}

	private function setResponseData()
	{
		if ($this->code === 400) {
			$this->data['error'] = $this->validator->messages()->all();

			return;
		}

		$this->data['message'] = "The given data was invalid.";
		$this->data['errors']  = [];

		$messages = $this->validator->messages()->all();

		if (!empty($messages)) {
			foreach ($messages as $key => $message) {
				$this->data['errors'][$key] = $message;
			}
		}
	}

	/**
	 * @param $request
	 *
	 * @return Response
	 */
	public function toResponse($request): Response
	{
		$this->setResponseData();

		return response()->json($this->data, $this->code);
	}
}
