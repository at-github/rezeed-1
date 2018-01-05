<?php
namespace Common;

use Exception;

class Server
{
    const SUB_DOMAIN = '/rezeed';

    private $uri;
    private $method;

    public function __construct()
    {
        if (!isset($_SERVER['REQUEST_URI']))
            throw new Exception('REQUEST_URI index not found');

        if (!isset($_SERVER['REQUEST_METHOD']))
            throw new Exception('REQUEST_METHOD index not found');

        $this->uri = preg_replace(
            '/\\' . self::SUB_DOMAIN . '/', '',
            $_SERVER['REQUEST_URI']
        );
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}