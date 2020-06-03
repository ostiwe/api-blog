<?php

namespace Blog\Controller;

use Blog\Model\PostModel;
use Slim\Psr7\Request;
use Slim\Psr7\Response;


class MainController extends BaseController
{

	public function post(Request $request, Response $response)
	{
		$body = json_decode(file_get_contents('php://input'), true);
		$posts = (new PostModel())->getPosts($body['page']);
		$response->getBody()->write(json_encode(['count' => count($posts), 'items' => $posts]));
		return $response->withHeader('Content-Type', 'application/json');

	}

}