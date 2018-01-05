<?php

use PHPUnit\Framework\TestCase,
    Common\Response,
    Module\User\UserController;

class UserControllerTest extends TestCase
{
    static public $userController;

    static public function setUpBeforeClass()
    {
        self::$userController = new UserController();
    }

    public function testSetResponse()
    {
        $responseMock = $this->createMock(Response::class);
        $setResponseReturn = (new UserController())->setResponse($responseMock);

        $this->assertInstanceOf(UserController::class, $setResponseReturn);
    }

    public function testSetUserModel()
    {
        $setResponseReturn = (new UserController())->setUserModel(null);

        $this->assertInstanceOf(UserController::class, $setResponseReturn);
    }

    public function testGetInfoFromId()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(200, ['id' => 2, 'name' => 'tarik', 'email' => 'tarik@e.mail']);

        $response = self::$userController
                        ->setResponse($responseMock)
                        ->getInfoFromId(2);
    }
}
