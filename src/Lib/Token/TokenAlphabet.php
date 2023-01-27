<?php

namespace IDT\LaravelCommon\Lib\Token;
enum TokenAlphabet: string
{
	case AlphaNumeric = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	case Alpha        = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	case HexDec       = '0123456789abcdef';
	case Numeric      = '0123456789';
	case NoZero       = '123456789';
	case Distinct     = '2345679ACDEFHJKLMNPRSTUVWXYZ';
}
