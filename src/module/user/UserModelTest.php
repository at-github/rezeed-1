<?php

use PHPUnit\Framework\TestCase,
    Module\User\UserModel;

class UserModelTest extends TestCase
{
    public function testGetInfoFromId()
    {
        $id = 33;
        $infoFromId = (new UserModel())->getInfoFromId($id);
        $this->assertSame(['id' => $id, 'name' => 'tarik', 'email' => 'tarik@e.mail'], $infoFromId);
    }
}
