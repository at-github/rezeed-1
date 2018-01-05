<?php

use PHPUnit\Framework\TestCase,
    Common\Router;

class RouterTest extends TestCase
{
    public function testSetServer()
    {
        $serverMock = $this->createMock(Common\Server::class);
        $router = new Router();
        $response = $router->setServer($serverMock);

        $this->assertInstanceOf(Router::class, $response);
    }

    public function testDispatch()
    {
        $this->markTestSkipped('Need a fabric to inject controller');
    }

}
