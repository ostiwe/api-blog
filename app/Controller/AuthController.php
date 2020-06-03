<?php


namespace Blog\Controller;

use Blog\Helper\SetTypesHelper;
use Blog\Model\AccessTokenModel;
use Blog\Model\UserModel;
use R;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AuthController
{


	private function getParsedBody()
	{
		return json_decode(file_get_contents("php://input"), true);
	}

	private function checkRegisterFields($params)
	{
		$errors = [];
		$userLogin = preg_replace('/\s+/', ' ', $params['login']);
		$email = preg_replace('/\s+/', ' ', $params['email']);
//		$first_name = preg_replace('/\s+/', ' ', $params['first_name']);
//		$last_name = preg_replace('/\s+/', ' ', $params['last_name']);
//		$sex = (int)$params['sex'];
		$age = (int)$params['age'];
		$password = preg_replace('/\s+/', ' ', $params['password']);


		if (empty($userLogin)) $errors[] = 'login need';
		if (empty($email)) $errors[] = 'email need';
//		if (empty($first_name)) $errors[] = 'first_name need';
//		if (empty($last_name)) $errors[] = 'last_name need';
//		if (!($sex === 0 || $sex === 1)) $errors[] = 'sex need';
		if (empty($age)) $errors[] = 'age need';
		if (empty($password)) $errors[] = 'password need';

		return $errors;
	}

	private function checkLoginFields($params)
	{
		$errors = [];
		$userLogin = preg_replace('/\s+/', ' ', $params['login']);
		$password = preg_replace('/\s+/', ' ', $params['password']);
		if (empty($userLogin)) $errors[] = 'login need';
		if (empty($password)) $errors[] = 'password need';
		return $errors;
	}

	public function register(Request $request, Response $response)
	{
		if ($request->hasHeader('Content-Type') && strtolower($request->getHeaderLine('Content-Type')) === "application/json") {
			$body = self::getParsedBody();

			$errors = self::checkRegisterFields($body);

			if (count($errors) !== 0) {
				$response->getBody()->write(json_encode(['status' => 'client_error', 'message' => $errors]));
				return $response->withStatus(400)->withAddedHeader('Content-Type', 'Application\json');
			}

			(new UserModel())
				->setUsername($body['login'])
				->setEmail($body['email'])
				->setPassword($body['password'])
				->setFirstName($body['first_name'])
				->setLastName($body['last_name'])
				->setAge($body['age'])
				->setSex($body['sex'])
				->setMask(0)
				->create();

			$response->getBody()->write(json_encode([
				'status' => 'success',
			]));
			return $response->withStatus(200)->withAddedHeader('Content-Type', 'Application\json');

		}
		$response->getBody()->write(json_encode(['status' => 'client_error', 'message' => 'Invalid request body']));
		return $response->withStatus(400)->withAddedHeader('Content-Type', 'Application\json');
	}

	public function login(Request $request, Response $response)
	{
		if ($request->hasHeader('Content-Type') && strtolower($request->getHeaderLine('Content-Type')) === "application/json") {
			$body = self::getParsedBody();

			$errors = self::checkLoginFields($body);

			if (count($errors) !== 0) {
				$response->getBody()->write(json_encode(['status' => 'client_error',
					'message' => $errors]));
				return $response->withStatus(400)->withAddedHeader('Content-Type', 'Application\json');
			}

			$user = R::findOne('user', 'username = ?', [$body['login']]);

			if ($user === NULL || $user['id'] === 0) {
				$response->getBody()->write(json_encode(['status' => 'auth_error',
					'message' => 'User not found or password entered incorrectly']));
				return $response->withStatus(200)->withAddedHeader('Content-Type', 'Application\json');
			}


			if (!password_verify($body['password'], $user['password'])) {
				$response->getBody()->write(json_encode(['status' => 'auth_error',
					'message' => 'User not found or password entered incorrectly', '_' => $body, '__' => $user->export()]));
				return $response->withStatus(200)->withAddedHeader('Content-Type', 'Application\json');
			}

			$userModel = (new  UserModel())->load($user['id']);
			$accessToken = (new AccessTokenModel())
				->setMask($user['mask'])
				->setCreated(time())
				->setExpired(time() + 129600)
				->setOwner($userModel)->create();
			if (!$accessToken) {
				$response->getBody()->write(json_encode(['status' => 'server_error',
					'message' => 'Try again later']));
				return $response->withStatus(500)->withAddedHeader('Content-Type', 'Application\json');
			}
			$response->getBody()->write(json_encode(['status' => 'success',
				'data' => [
					'user_info' => [
						'login' => $userModel->getUsername(),
						'uid' => $userModel->getUid(),
						'access_token' => $accessToken,
						'mask' => $userModel->getMask(),
					],
				]]));
			return $response->withStatus(200)->withAddedHeader('Content-Type', 'Application\json');

		}
		$response->getBody()->write(json_encode(['status' => 'client_error', 'message' => 'Invalid request body']));
		return $response->withStatus(400)->withAddedHeader('Content-Type', 'Application\json');

	}

	public function getInfo(Request $request, Response $response)
	{
		$body = self::getParsedBody();

		$token = (new AccessTokenModel())->load($body['access_token']);
		if (!$token) {
			$response->getBody()->write(json_encode(['status' => 'bad_request',
				'message' => 'access token not passed']));
			return $response;
		}

		$info = [
			'status' => 'success',
			'access_token' => [
				'access_token' => $token->getAccessToken(),
				'expired' => $token->getExpired(),
				'mask' => $token->getMask(),
			],
			'user_info' => [
				'login' => $token->getOwner()->getUsername(),
				'uid' => $token->getOwner()->getUid(),
				'mask' => $token->getOwner()->getMask(),
			],
		];

		$response->getBody()->write(json_encode(SetTypesHelper::handle($info)));

		return $response;
	}
}