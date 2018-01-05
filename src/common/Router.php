<?php

namespace Common;

use Exception,
    Module\User\UserController,
    Common\NotFoundController;

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

        (new NotFoundController($uri))->response();
    }
}
