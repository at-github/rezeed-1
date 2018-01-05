<?php

use PHPUnit\Framework\TestCase,
    Common\Server;

/**
 * ServerTest
 *
 * @package default
 * @author Tarik
 */
class ServerTest extends TestCase
{
    public function testConstructExceptionUri()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('REQUEST_URI');

        $server = new Server();
    }

    public function testConstructExceptionMethod()
    {
        $_SERVER['REQUEST_URI'] = '/foo';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('REQUEST_METHOD');

        $server = new Server();
    }

    public function testConstructNeedToCleanSubDomain()
    {
        $_SERVER['REQUEST_URI']    = '/rezeed/user/5';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $server = new Server();
        $this->assertEquals('/user/5', $server->getUri());
    }

    public function testConstructNoNeedToCleanSubDomain()
    {
        $_SERVER['REQUEST_URI']    = '/user/5';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $server = new Server();
        $this->assertEquals('/user/5', $server->getUri());
    }

    /**
     * @dataProvider getMethodProvider
     */
    public function testGetMethod($expectedMethod)
    {
        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['REQUEST_METHOD'] = $expectedMethod;

        $server = new Server();
        $this->assertEquals($expectedMethod, $server->getMethod());
    }

    public function getMethodProvider()
    {
        return [
            ['GET'], ['POST'], ['DELETE']
        ];
    }
}
