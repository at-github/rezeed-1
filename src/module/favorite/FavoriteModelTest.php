<?php

use PHPUnit\Framework\TestCase,
    Module\Favorite\FavoriteModel;

class FavoriteModelTest extends TestCase
{
    public function testGetFavoriteSongFromUserIdException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to execute query');

        $id = 11;
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with("SELECT title FROM song"
                    . " INNER JOIN favorite_song ON song.id = favorite_song.song_id"
                    . " INNER JOIN user ON user.id = favorite_song.user_id"
                    . " WHERE user.id = $id;")
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
                ->with("SELECT title FROM song"
                    . " INNER JOIN favorite_song ON song.id = favorite_song.song_id"
                    . " INNER JOIN user ON user.id = favorite_song.user_id"
                    . " WHERE user.id = $id;")
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
                ->with("SELECT title FROM song"
                    . " INNER JOIN favorite_song ON song.id = favorite_song.song_id"
                    . " INNER JOIN user ON user.id = favorite_song.user_id"
                    . " WHERE user.id = $id;")
                ->willReturn($pdoStatementMock);

        $this->assertEquals(
            $dataToRetrieve,
            (new FavoriteModel($pdoMock))->getFavoriteSongFromUserId($id)
        );
    }
}
