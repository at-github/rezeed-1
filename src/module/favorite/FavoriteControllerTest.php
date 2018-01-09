<?php

use PHPUnit\Framework\TestCase,
    Common\Response;
use Module\{
    Favorite\FavoriteController,
    Favorite\FavoriteModel,
    User\UserModel,
    Song\SongModel
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

    public function testSetUserModel()
    {
        $setUserModelReturn = (new FavoriteController())->setUserModel(null);

        $this->assertInstanceOf(FavoriteController::class, $setUserModelReturn);
    }

    public function testSetSongModel()
    {
        $setSongModelReturn = (new FavoriteController())->setSongModel(null);

        $this->assertInstanceOf(FavoriteController::class, $setSongModelReturn);
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

    /**
     * @dataProvider addSongProvider
     */
    public function testAddSongWrongParam($expectCode, $expectMessage, $post)
    {

        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with($expectCode, $expectMessage);

        self::$favoriteController
            ->setResponse($responseMock)
            ->addSong($post);
    }

    public function addSongProvider()
    {
        return [
            [422, ['message' => 'user_id is missing'],    []],
            [422, ['message' => 'user_id must be digit'], ['user_id' => 'foo']],
            [422, ['message' => 'song_id is missing'],    ['user_id' => '2']],
            [422, ['message' => 'song_id must be digit'], ['song_id' => 'foo', 'user_id' => '2']],
        ];
    }

    public function testAddSongUserNotFound()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(422,[ 'message' => 'user with id: 2 doesn\'t exist']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->expects($this->once())
                     ->method('getInfoFromId')
                     ->with('2')
                     ->willReturn(null);

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->addSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    public function testAddSongSongNotFound()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(422,[ 'message' => 'song with id: 3 doesn\'t exist']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->expects($this->once())
                      ->method('getInfoFromId')
                      ->with('2')
                      ->willReturn(true);


        $songModelMock = $this->createMock(SongModel::class);
        $songModelMock->expects($this->once())
                      ->method('getInfoFromId')
                      ->with('3')
                      ->willReturn(null);

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->setSongModel($songModelMock)
            ->addSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    public function testAddSongRunTimeRuntimeException()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(500,[ 'message' => 'error from model']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->method('getInfoFromId')
                      ->willReturn(true);


        $songModelMock = $this->createMock(SongModel::class);
        $songModelMock->method('getInfoFromId')
                      ->willReturn(true);

        $favoriteModelMock = $this->createMock(FavoriteModel::class);
        $favoriteModelMock->expects($this->once())
                          ->method('addSong')
                          ->with(2, 3)
                          ->will(
                              $this->throwException(new RuntimeException('error from model'))
                          );

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->setSongModel($songModelMock)
            ->setFavoriteModel($favoriteModelMock)
            ->addSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    public function testAddSongRunTimeException()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(422,[ 'message' => 'error from model']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->method('getInfoFromId')
                      ->willReturn(true);


        $songModelMock = $this->createMock(SongModel::class);
        $songModelMock->method('getInfoFromId')
                      ->willReturn(true);

        $favoriteModelMock = $this->createMock(FavoriteModel::class);
        $favoriteModelMock->expects($this->once())
                          ->method('addSong')
                          ->with(2, 3)
                          ->will(
                              $this->throwException(new Exception('error from model'))
                          );

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->setSongModel($songModelMock)
            ->setFavoriteModel($favoriteModelMock)
            ->addSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    public function testAddSongSuccess()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(201,['foo', 'bar']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->method('getInfoFromId')
                      ->willReturn(true);


        $songModelMock = $this->createMock(SongModel::class);
        $songModelMock->method('getInfoFromId')
                      ->willReturn(true);

        $favoriteModelMock = $this->createMock(FavoriteModel::class);
        $favoriteModelMock->expects($this->once())
                          ->method('addSong')
                          ->with(2, 3)
                          ->willReturn(
                              ['foo', 'bar']
                          );

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->setSongModel($songModelMock)
            ->setFavoriteModel($favoriteModelMock)
            ->addSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    /**
     * @dataProvider deleteSongProvider
     */
    public function testDeleteSongWrongParam($expectCode, $expectMessage, $delete)
    {

        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with($expectCode, $expectMessage);

        self::$favoriteController
            ->setResponse($responseMock)
            ->deleteSong($delete);
    }

    public function deleteSongProvider()
    {
        return [
            [422, ['message' => 'user_id is missing'],    []],
            [422, ['message' => 'user_id must be digit'], ['user_id' => 'foo']],
            [422, ['message' => 'song_id is missing'],    ['user_id' => '2']],
            [422, ['message' => 'song_id must be digit'], ['song_id' => 'foo', 'user_id' => '2']],
        ];
    }

    public function testDeleteSongUserNotFound()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(422,[ 'message' => 'user with id: 2 doesn\'t exist']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->expects($this->once())
                      ->method('getInfoFromId')
                      ->with('2')
                      ->willReturn(null);

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->deleteSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    public function testDeleteSongSongNotFound()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(422,[ 'message' => 'song with id: 3 doesn\'t exist']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->expects($this->once())
                      ->method('getInfoFromId')
                      ->with('2')
                      ->willReturn(true);


        $songModelMock = $this->createMock(SongModel::class);
        $songModelMock->expects($this->once())
                      ->method('getInfoFromId')
                      ->with('3')
                      ->willReturn(null);

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->setSongModel($songModelMock)
            ->deleteSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    public function testDeleteSongRunTimeRuntimeException()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(500,[ 'message' => 'error from model']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->method('getInfoFromId')
                      ->willReturn(true);


        $songModelMock = $this->createMock(SongModel::class);
        $songModelMock->method('getInfoFromId')
                      ->willReturn(true);

        $favoriteModelMock = $this->createMock(FavoriteModel::class);
        $favoriteModelMock->expects($this->once())
                          ->method('deleteSong')
                          ->with(2, 3)
                          ->will(
                              $this->throwException(new RuntimeException('error from model'))
                          );

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->setSongModel($songModelMock)
            ->setFavoriteModel($favoriteModelMock)
            ->deleteSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    public function testDeleteSongRunTimeException()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(422,[ 'message' => 'error from model']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->method('getInfoFromId')
                      ->willReturn(true);


        $songModelMock = $this->createMock(SongModel::class);
        $songModelMock->method('getInfoFromId')
                      ->willReturn(true);

        $favoriteModelMock = $this->createMock(FavoriteModel::class);
        $favoriteModelMock->expects($this->once())
                          ->method('deleteSong')
                          ->with(2, 3)
                          ->will(
                              $this->throwException(new Exception('error from model'))
                          );

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->setSongModel($songModelMock)
            ->setFavoriteModel($favoriteModelMock)
            ->deleteSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    public function testDeleteSongSuccess()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(200,['foo', 'bar']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->method('getInfoFromId')
                      ->willReturn(true);


        $songModelMock = $this->createMock(SongModel::class);
        $songModelMock->method('getInfoFromId')
                      ->willReturn(true);

        $favoriteModelMock = $this->createMock(FavoriteModel::class);
        $favoriteModelMock->expects($this->once())
                          ->method('deleteSong')
                          ->with(2, 3)
                          ->willReturn(
                              ['foo', 'bar']
                          );

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->setSongModel($songModelMock)
            ->setFavoriteModel($favoriteModelMock)
            ->deleteSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }

    public function testDeleteSongSuccessFavoriteEmpty()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->expects($this->once())
                     ->method('json')
                     ->with(404, ['message' => 'no favorite for user id: 2']);

        $userModelMock = $this->createMock(UserModel::class);
        $userModelMock->method('getInfoFromId')
                      ->willReturn(true);


        $songModelMock = $this->createMock(SongModel::class);
        $songModelMock->method('getInfoFromId')
                      ->willReturn(true);

        $favoriteModelMock = $this->createMock(FavoriteModel::class);
        $favoriteModelMock->expects($this->once())
                          ->method('deleteSong')
                          ->with(2, 3);

        self::$favoriteController
            ->setResponse($responseMock)
            ->setUserModel($userModelMock)
            ->setSongModel($songModelMock)
            ->setFavoriteModel($favoriteModelMock)
            ->deleteSong([
                'song_id' => '3', 'user_id' => '2'
            ]);
    }
}
