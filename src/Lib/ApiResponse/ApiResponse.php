<?php

namespace IDT\LaravelCommon\Lib\ApiResponse;


use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use IDT\LaravelCommon\Lib\Utils\Enums\HttpStatusCode;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template T
 */
class ApiResponse implements Responsable
{
	/**
	 * @var array{success:bool, data:T, error:array<string, string>|null}
	 */
	protected array $data = [];

	protected int $code = 200;

	/**
	 * @param T                  $data
	 * @param int|HttpStatusCode $code
	 *
	 * @return static
	 */
	public static function withData($data, HttpStatusCode|int $code = 200): static
	{
		$response = new static();
		$response->statusCode($code);
		$response->data($data);

		return $response;
	}

	/**
	 * @param int|HttpStatusCode $code
	 *
	 * @return ApiResponse
	 */
	public function statusCode(HttpStatusCode|int $code): static
	{
		if ($code instanceof HttpStatusCode) {
			$code = $code->value;
		}

		$this->code = $code;

		return $this;
	}


	/**
	 * @param callable|bool      $condition
	 * @param int|HttpStatusCode $code
	 *
	 * @return $this
	 */
	public function statusCodeWhen(callable|bool $condition, HttpStatusCode|int $code): static
	{
		if (value($condition)) {
			$this->statusCode(value($code));
		}

		return $this;
	}

	/**
	 * @param T $data
	 *
	 * @return ApiResponse<T>
	 */
	public function data($data): static
	{
		if ($data instanceof Collection) {
			$data = $data->toArray();
		}

		$this->data['data'] = $data;

		return $this;
	}

	/**
	 * @param callable|bool $condition
	 * @param T|callable    $data
	 *
	 * @return $this
	 */
	public function dataWhen(callable|bool $condition, mixed $data): static
	{
		if (value($condition)) {
			$this->data(value($data));
		}

		return $this;
	}

	public function additional($data): static
	{
		if ($data instanceof Collection) {
			$data = $data->toArray();
		}

		$this->data = array_merge($this->data, $data);

		return $this;
	}

	/**
	 * @param callable|bool $condition
	 * @param callable|T    $data
	 *
	 * @return $this
	 */
	public function additionalWhen(callable|bool $condition, mixed $data): static
	{
		if (value($condition)) {
			$this->additional(value($data));
		}

		return $this;
	}


	/**
	 * @param class-string $class
	 * @param              $data
	 *
	 * @return ApiResponse
	 */
	public function resource(string $class, $data)
	{
		/** @var JsonResource $resource */
		$resource = null;

		if ($data instanceof Collection) {
			$resource = $class::collection($data);
		} else {
			$resource = new $class($data);
		}

		/** @var JsonResource $resource */
		$this->data['data'] = $resource->resolve(request());

		return $this;
	}

	/**
	 * @param $request
	 *
	 * @return Response
	 */
	public function toResponse($request): Response
	{
		return response()->json($this->data, $this->code);
	}

	public function getData(): array
	{
		return $this->data;
	}

}
