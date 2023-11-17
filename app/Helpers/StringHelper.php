<?php
namespace App\Helpers;

class StringHelper {


	public static function getRandomString($n = 7) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $n; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}

		return $randomString;
	}

	public static function formatPhone($hp)
	{
		$phone = $hp;
		$phones = str_split($hp);
		if ($phones[0] == 0) {
			$phones[0] = 62;
			$phone = implode('',$phones);
		}
		return $phone;
	}

	public static function replaceCommaWithDot($value)
	{
		$value = str_replace(',', '.', $value);
		return $value;
	}
}
