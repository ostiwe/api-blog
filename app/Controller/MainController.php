<?php

namespace Blog\Controller;

use Blog\Helper\SetTypesHelper;
use Blog\Model\PostModel;
use Blog\Model\UserModel;
use Slim\Psr7\Request;
use Slim\Psr7\Response;


class MainController extends BaseController
{

	public function mainC(Request $request, Response $response)
	{
		$mask = UserModel::CAN_CREATE_POST | UserModel::CAN_CREATE_COMMENT;
		$user = (new UserModel())
			->setUsername('ostiwe')
			->setEmail('admin@ostiwe.ru')
			->setPassword('123455')
			->setFirstName('Tester')
			->setLastName('Test')
			->setAge(19)
			->setSex(0)
			->setMask($mask)->create();

		$post = (new  PostModel())
			->setAuthor($user)
			->setTitle("Test post")
			->setText("Osjkaoiads adokasd aidaod anduiahd iahduia diaduia aijdiuah dasdiandoi ospdjaoi idajdoia")
			->setViews(0)->create();

		$response->getBody()->write(json_encode(['s' => 'sd']));

		return $response->withHeader('Content-Type', 'application/json');
	}

	public function post(Request $request, Response $response)
	{
		$body = json_decode(file_get_contents('php://input'), true);
		$posts = (new PostModel())->getPosts($body['page']);
		$response->getBody()->write(json_encode(['_' => $body, 'count' => count($posts), 'items' => $posts]));
		return $response->withHeader('Content-Type', 'application/json');

	}

	public function user(Request $request, Response $response)
	{
		try {
			$user = (new UserModel())->load(5);
			$response->getBody()->write(json_encode(['s' =>
				SetTypesHelper::handle($user->getBean()->export())]));
		} catch (\Exception $e) {
			$response->getBody()->write(json_encode(['s' => 'sd']));
		}


		return $response->withHeader('Content-Type', 'application/json');

	}

}