<?php
namespace App\Helpers;


class SqlHelper
{
	public static function getSqlWithBindings($query)
	{
		if(!$query) {
			throw new \Exception("query is required");
		}
		return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
			return is_numeric($binding) ? $binding : "'{$binding}'";
		})->toArray());
	}
}
