<?php

use PHPUnit\Framework\TestCase,
    Common\Response;
use Module\{
    Song\SongController,
    Song\SongModel
};

class SongControllerTest extends TestCase
{
    static public $songController;

    static public function setUpBeforeClass()
    {
        self::$songController = new SongController();
    }

    public function testSetResponse()
    {
        $responseMock = $this->createMock(Response::class);
        $setResponseReturn = (new SongController())->setResponse($responseMock);

        $this->assertInstanceOf(SongController::class, $setResponseReturn);
    }

    public function testSetSongModel()
    {
        $setResponseReturn = (new SongController())->setSongModel(null);

        $this->assertInstanceOf(SongController::class, $setResponseReturn);
    }

    public function testGetInfoFromIdError()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(500, ['message' => 'error from model']);

        $modelMock = $this->createMock(SongModel::class);
        $modelMock->expects($this->once())
                  ->method('getInfoFromId')
                  ->with(2)
                  ->will($this->throwException(new RuntimeException('error from model')));

        self::$songController
            ->setResponse($responseMock)
            ->setSongModel($modelMock)
            ->getInfoFromId(2);
    }

    public function testGetInfoFromIdDataNotFound()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(404, ['message' => 'no song with id: 2']);

        $modelMock = $this->createMock(SongModel::class);
        $modelMock->expects($this->once())
                  ->method('getInfoFromId')
                  ->with(2)
                  ->willReturn(null);

        self::$songController
            ->setResponse($responseMock)
            ->setSongModel($modelMock)
            ->getInfoFromId(2);
    }

    public function testGetInfoFromIdDataFound()
    {
        $songData = ['id' => 2, 'title' => 'song', 'duration' => 222];

        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json');

        $modelMock = $this->createMock(SongModel::class);
        $modelMock->expects($this->once())
                     ->method('getInfoFromId')
                     ->with(2)
                     ->willReturn($songData);

        self::$songController
            ->setResponse($responseMock)
            ->setSongModel($modelMock)
            ->getInfoFromId(2);
    }
}
