<?php

use PHPUnit\Framework\TestCase,
    Module\Favorite\FavoriteModel;

class FavoriteModelTest extends TestCase
{
    public function setUp()
    {
        $this->querySelectFavorite = 'SELECT song.id, song.title FROM song'
            . ' INNER JOIN favorite_song ON song.id = favorite_song.song_id'
            . ' INNER JOIN user ON user.id = favorite_song.user_id'
            . ' WHERE user.id = %d;';
    }

    public function testGetFavoriteSongFromUserIdException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to execute query');

        $id = 11;
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with(sprintf($this->querySelectFavorite, $id))
                ->will($this->throwException(new Exception));

        (new FavoriteModel($pdoMock))->getFavoriteSongFromUserId($id);
    }

    public function testGetFavoriteSongFromUserIdDataNotFound()
    {
        $id = 22;

        $pdoStatementMock = $this->createMock(PDOStatement::class);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with(sprintf($this->querySelectFavorite, $id))
                ->willReturn($pdoStatementMock);

        $this->assertNull((new FavoriteModel($pdoMock))->getFavoriteSongFromUserId($id));
    }

    public function testGetFavoriteSongFromUserIdDataFound()
    {
        $id = 33;
        $dataToRetrieve = [['title' => 'song1'], ['title' => 'song2']];

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once())
                ->method('fetchAll')
                ->with(PDO::FETCH_OBJ)
                ->willReturn($dataToRetrieve);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with(sprintf($this->querySelectFavorite, $id))
                ->willReturn($pdoStatementMock);

        $this->assertEquals(
            $dataToRetrieve,
            (new FavoriteModel($pdoMock))->getFavoriteSongFromUserId($id)
        );
    }

    public function testAddSongCheckAlreadyExistFail()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to execute query');

        $userId = 11;
        $songId = 22;

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with('SELECT count(*) FROM favorite_song WHERE user_id = 11 AND song_id = 22;')
                ->will($this->throwException(new Exception));

        (new FavoriteModel($pdoMock))->addSong($userId, $songId);
    }

    public function testAddSongAlreadyExist()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('song with id: 22 already in favorite of user: 11');

        $userId = 11;
        $songId = 22;

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once())
                ->method('fetch')
                ->with(PDO::FETCH_COLUMN)
                ->willReturn(1);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->willReturn($pdoStatementMock);

        (new FavoriteModel($pdoMock))->addSong($userId, $songId);
    }

    public function testAddSongInsertFail()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to execute query');

        $userId = 11;
        $songId = 22;

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once())
                         ->method('fetch')
                         ->with(PDO::FETCH_COLUMN)
                         ->willReturn(0);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->at(0))
                ->method('query')
                ->willReturn($pdoStatementMock);

        $pdoMock->expects($this->at(1))
                ->method('query')
                ->will($this->throwException(new Exception));

        (new FavoriteModel($pdoMock))->addSong($userId, $songId);
    }

    public function testAddSongInsertSuccess()
    {
        $userId = 11;
        $songId = 22;

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once())
                ->method('fetch')
                ->with(PDO::FETCH_COLUMN)
                ->willReturn(0); // No song already in favorite

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->at(0))
                ->method('query')
                ->willReturn($pdoStatementMock); // Insert

        $pdoMock->expects($this->at(2))
                ->method('query')
                ->willReturn($pdoStatementMock); // Query list favorites song after insert

        (new FavoriteModel($pdoMock))->addSong($userId, $songId);
    }
}
