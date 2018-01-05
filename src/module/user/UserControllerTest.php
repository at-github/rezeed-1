<?php

use PHPUnit\Framework\TestCase,
    Common\Response,
    Module\User\UserController,
    Module\User\UserModel;

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
        $userData = ['id' => 2, 'name' => 'tarik', 'email' => 'tarik@e.mail'];

        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json');

        $modelMock = $this->createMock(UserModel::class);
        $modelMock->expects($this->once())
                     ->method('getInfoFromId')
                     ->with(2)
                     ->willReturn($userData);

        $response = self::$userController
                        ->setResponse($responseMock)
                        ->setUserModel($modelMock)
                        ->getInfoFromId(2);
    }
}
