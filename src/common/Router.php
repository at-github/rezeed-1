<?php

namespace Common;

use Exception;
use Module\{
    User\UserModel,
    User\UserController
};
use Common\{
    NotFoundController,
    Response
};

class Router
{
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_DELETE = 'DELETE';

    private $server;

    public function setServer(ServerInterface $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function dispatch()
    {
        $uri    = $this->server->getUri();
        $method = $this->server->getMethod();
        $response = new Response();

        // user queries
        // route: GET /user/:id
        if (
            $method === self::METHOD_GET &&
            preg_match('/^\/user\/(\d+)$/', $uri, $slugs)
        ) {
            (new UserController())
                ->setResponse($response)
                ->setUserModel(new UserModel())
                ->getInfoFromId($slugs[1]);
        } else {
            (new NotFoundController())
                ->setResponse($response)
                ->response();
        }
    }
}
