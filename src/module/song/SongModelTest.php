<?php

use PHPUnit\Framework\TestCase,
    Module\Song\SongModel;

class SongModelTest extends TestCase
{
    public function testGetInfoFromIdException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to execute query');

        $id = 11;
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with("SELECT id, title, duration FROM song WHERE id=$id;")
                ->will($this->throwException(new Exception));

        (new SongModel($pdoMock))->getInfoFromId($id);
    }

    public function testGetInfoFromIdDataNotFound()
    {
        $id = 22;

        $pdoStatementMock = $this->createMock(PDOStatement::class);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with("SELECT id, title, duration FROM song WHERE id=$id;")
                ->willReturn($pdoStatementMock);

        $this->assertNull((new SongModel($pdoMock))->getInfoFromId($id));
    }

    public function testGetInfoFromIdDataFound()
    {
        $id = 33;
        $dataToRetrieve = ['id' => 0, 'title' => 'foo', 'duration' => 'bar'];

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once())
                ->method('fetch')
                ->with(PDO::FETCH_OBJ)
                ->willReturn((object) $dataToRetrieve);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with("SELECT id, title, duration FROM song WHERE id=$id;")
                ->willReturn($pdoStatementMock);

        $this->assertEquals($dataToRetrieve, (new SongModel($pdoMock))->getInfoFromId($id));
    }
}
