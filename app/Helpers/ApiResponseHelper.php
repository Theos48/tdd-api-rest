<?php
	
	namespace App\Helpers;
	
	use Illuminate\Http\Exceptions\HttpResponseException;
	use Illuminate\Http\JsonResponse;
	use Illuminate\Http\Resources\Json\JsonResource;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Log;
	use Symfony\Component\HttpFoundation\Response;
	use Throwable;
	
	class ApiResponseHelper {
		/**
		 * Returns a JSON response for successful operations.
		 *
		 * @param array|JsonResource $data
		 * @param string $message
		 * @param int $code
		 *
		 * @return JsonResponse
		 */
		public static function successResponse(array|JsonResource $data = [], string $message = 'success', int $code = Response::HTTP_OK): JsonResponse {
			
			if ($data instanceof JsonResource) {
				$data = $data->resolve();
			}
			
			return response()->json([
				'data' => $data,
				'message' => $message,
				'status_code' => $code,
			], $code);
		}
		
		/**
		 * Returns a JSON response for error operations.
		 *
		 * @param string $message
		 * @param int $code
		 * @param array $errors
		 *
		 * @return JsonResponse
		 */
		public static function errorResponse(string $message = 'Something went wrong!', int $code = Response::HTTP_INTERNAL_SERVER_ERROR, array $errors = []): JsonResponse {
			return response()->json([
				'message' => $message,
				'status_code' => $code,
				'errors' => $errors,
			], $code);
		}
		
		/**
		 * Logs an error and throws an HttpResponseException.
		 *
		 * @param Throwable $exception
		 * @param string $message
		 * @param int $code
		 *
		 * @return never
		 */
		public static function logAndThrowError(Throwable $exception, string $message = 'Something went wrong! Process not completed', int $code = Response::HTTP_INTERNAL_SERVER_ERROR): never {
			Log::error($exception->getMessage(), ['exception' => $exception]);
			throw new HttpResponseException(self::errorResponse($message, $code));
		}
		
		/**
		 * Rolls back the database transaction, logs an error, and throws an HttpResponseException.
		 *
		 * @param Throwable $exception
		 * @param string $message
		 * @param int $code
		 *
		 * @return never
		 */
		public static function rollBackAndErrorResponse(Throwable $exception, string $message = 'Something went wrong! Process not completed', int $code = Response::HTTP_INTERNAL_SERVER_ERROR): never {
			DB::rollBack();
			self::logAndThrowError($exception, $message, $code);
		}
	}