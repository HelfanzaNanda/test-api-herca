<?php
namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class ResponseHelper {
	public static function success($message = null, $data = null, $sql = null)
	{
		$result = [
			'status' => true,
			// 'code' => 200,
			'message' => $message ?? 'Successfully',
			'data' => $data ?? (object)[]
		];
		if (env('SHOW_ERROR') && $sql) {
			$result['sql'] = SqlHelper::getSqlWithBindings($sql);
		}
		return $result;
	}
	public static function error($message = null, $th = null, $data = null)
	{
		$result = [
			'status' => false,
			// 'code' => 500,
			'message' => 'Server Error',
			'data' => $data ?? (object)[]
		];
		if (env('SHOW_ERROR')) {
			$result['error'] = $th;
			if ($th instanceof Throwable) {
				$result['error'] = [
					'message' => $th->getMessage(),
					'file' => $th->getFile(),
					'line' => $th->getLine(),
				];
			}
		}
		$class = self::get_calling_class(); // app/Http/Controllers/API/AssetClaimController.php
		Log::channel('error')->error($class, $result);
		// Log::error($class, $result);

		return $result;
	}

	public static function warning($message = null, $data = null, $errors = null, $validations = null, $code = 400, $sql = null)
	{
		$result = [
			'status' => false,
			// 'code' => $code,
			'message' => $message ?? 'Server Error',
			'data' => $data ?? (object)[],
            'error' => $errors ?? (object)[],
            'validations' => $validations ?? (object)[]
		];
		if (env('APP_DEBUG') && $sql) {
			$result['sql'] = SqlHelper::getSqlWithBindings($sql);
		}
		return $result;
	}

	protected static function get_calling_class() {
		$trace = debug_backtrace();
		$class = $trace[1]['class'];

		for ( $i=1; $i<count( $trace ); $i++ ) {
			if ( isset( $trace[$i] ) ) // is it set?
				 if ( $class != $trace[$i]['class'] ) // is it a different class
					 return $trace[$i]['class'];
		}
	}

}
