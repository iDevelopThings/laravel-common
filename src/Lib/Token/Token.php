<?php

namespace IDT\LaravelCommon\Lib\Token;

class Token
{
	public TokenAlphabet $alphabet;

	public int $length;

	public function __construct(TokenAlphabet $alphabet = TokenAlphabet::HexDec, int $length = 32)
	{
		$this->alphabet = $alphabet;
		$this->length   = $length;
	}

	private function crypto_rand_secure($min, $max)
	{
		$range = $max - $min;
		if ($range < 1) {
			return $min;
		}
		$log    = ceil(log($range, 2));
		$bytes  = (int)($log / 8) + 1; // length in bytes
		$bits   = (int)$log + 1; // length in bits
		$filter = (int)(1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd > $range);

		return $min + $rnd;
	}

	public function create(): string
	{
		$token = '';
		$max   = strlen($this->alphabet->value) - 1;

		for ($i = 0; $i < $this->length; $i++) {
			$token .= $this->alphabet->value[$this->crypto_rand_secure(0, $max)];
		}

		return $token;
	}

	public static function generate(int $length = 32, TokenAlphabet $alphabet = TokenAlphabet::HexDec): string
	{
		return (new static($alphabet, $length))->create();
	}
}
