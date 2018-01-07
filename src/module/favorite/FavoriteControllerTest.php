<?php

use PHPUnit\Framework\TestCase,
    Common\Response;
use Module\{
    Favorite\FavoriteController,
    Favorite\FavoriteModel
};

class FavoriteControllerTest extends TestCase
{
    static public $favoriteController;

    static public function setUpBeforeClass()
    {
        self::$favoriteController = new FavoriteController();
    }

    public function testSetResponse()
    {
        $responseMock = $this->createMock(Response::class);
        $setResponseReturn = (new FavoriteController())->setResponse($responseMock);

        $this->assertInstanceOf(FavoriteController::class, $setResponseReturn);
    }

    public function testSetFavoriteModel()
    {
        $setResponseReturn = (new FavoriteController())->setFavoriteModel(null);

        $this->assertInstanceOf(FavoriteController::class, $setResponseReturn);
    }

    /**
     * @dataProvider convertTimeForHumanProvider
     */
    public function testConvertTimeForHuman($expect, $mSeconds)
    {
        $favoriteController = new FavoriteController();

        $this->assertEquals(
            $expect,
            $favoriteController->convertTimeForHuman($mSeconds)
        );
    }

    public function convertTimeForHumanProvider()
    {
        return [
            ['0:0', 0],
            ['1:0', 60],
            ['1:30', 90]
        ];
    }

    public function testGetInfoFromIdError()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(500, ['message' => 'error from model']);

        $modelMock = $this->createMock(FavoriteModel::class);
        $modelMock->expects($this->once())
                  ->method('getFavoriteSongFromUserId')
                  ->with(2)
                  ->will($this->throwException(new RuntimeException('error from model')));

        self::$favoriteController
            ->setResponse($responseMock)
            ->setFavoriteModel($modelMock)
            ->getInfoFromId(2);
    }

    public function testGetInfoFromIdDataNotFound()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(404, ['message' => 'no favorite for user id: 2']);

        $modelMock = $this->createMock(FavoriteModel::class);
        $modelMock->expects($this->once())
                  ->method('getFavoriteSongFromUserId')
                  ->with(2)
                  ->willReturn(null);

        self::$favoriteController
            ->setResponse($responseMock)
            ->setFavoriteModel($modelMock)
            ->getInfoFromId(2);
    }

    public function testGetInfoFromIdDataFound()
    {
        $favoriteData = ['id' => 2, 'title' => 'favorite', 'duration' => 222];

        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json');

        $modelMock = $this->createMock(FavoriteModel::class);
        $modelMock->expects($this->once())
                     ->method('getFavoriteSongFromUserId')
                     ->with(2)
                     ->willReturn($favoriteData);

        self::$favoriteController
            ->setResponse($responseMock)
            ->setFavoriteModel($modelMock)
            ->getInfoFromId(2);
    }
}
