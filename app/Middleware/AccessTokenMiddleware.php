<?php


namespace Blog\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use R;
use Slim\Psr7\Response;

class AccessTokenMiddleware
{
	public function __invoke(Request $request, RequestHandler $handler)
	{
		$body = json_decode($request->getBody(), true);
		$response = new Response();

		if (!self::hasBody($body)) {
			$response->getBody()->write(json_encode(['status' => 'bad_request',
				'message' => 'Request body is empty']));
			return $response->withAddedHeader('Content-Type', 'application/json');
		}
		$token = R::findOne('token', 'access_token = ?', [$body['access_token']]);

		if (!self::issetToken($token)) {
			$response->getBody()->write(json_encode(['status' => 'bad_request',
				'message' => 'No access_token passed']));
			return $response->withAddedHeader('Content-Type', 'application/json');
		}
		if (self::hasExpired($token)) {
			$response->getBody()->write(json_encode(['status' => 'bad_request',
				'message' => 'Access token expired']));
			return $response->withAddedHeader('Content-Type', 'application/json');
		}

		$response = $handler->handle($request);

		return $response->withAddedHeader('Content-Type', 'application/json');
	}

	private function hasBody($body)
	{
		if ($body === NULL || empty($body)) {
			return false;
		}
		return true;
	}

	private function issetToken($tokenData)
	{

		if (!$tokenData || $tokenData['id'] === 0) {
			return false;
		}
		return true;
	}

	private function hasExpired($tokenData)
	{
		$currentTime = time();
		if (!($tokenData['expired'] < $currentTime)) {
			return false;
		}
		return true;
	}
}