<?php

use PHPUnit\Framework\TestCase,
    Module\User\UserModel;

class UserModelTest extends TestCase
{
    public function testGetInfoFromIdException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to execute query');

        $id = 11;
        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with("SELECT id, name, email FROM user WHERE id=$id;")
                ->will($this->throwException(new Exception));

        (new UserModel($pdoMock))->getInfoFromId($id);
    }

    public function testGetInfoFromIdDataNotFound()
    {
        $id = 22;

        $pdoStatementMock = $this->createMock(PDOStatement::class);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with("SELECT id, name, email FROM user WHERE id=$id;")
                ->willReturn($pdoStatementMock);

        $this->assertNull((new UserModel($pdoMock))->getInfoFromId($id));
    }

    public function testGetInfoFromIdDataFound()
    {
        $id = 33;
        $dataToRetrieve = ['id' => 0, 'name' => 'foo', 'email' => 'bar'];

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once())
                ->method('fetch')
                ->with(PDO::FETCH_OBJ)
                ->willReturn((object) $dataToRetrieve);

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('query')
                ->with("SELECT id, name, email FROM user WHERE id=$id;")
                ->willReturn($pdoStatementMock);

        $this->assertEquals($dataToRetrieve, (new UserModel($pdoMock))->getInfoFromId($id));
    }
}
