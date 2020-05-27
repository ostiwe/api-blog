<?php

namespace Blog\Controller;

use Blog\Models\UserModel;
use Slim\Psr7\Request;
use Slim\Psr7\Response;


class MainController extends BaseController
    {

        public function mainC(Request $request, Response $response)
        {
            $us = new UserModel();
            $s = $us->load();
            $response->getBody()->write(json_encode(['s' => $s]));

            return $response->withHeader('Content-Type', 'application/json');
        }

    }