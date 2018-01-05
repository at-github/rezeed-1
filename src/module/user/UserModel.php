<?php

namespace Module\User;

class UserModel
{
    public function getInfoFromId(int $id)
    {
        //TODO sql query

        return ['id' => $id, 'name' => 'tarik', 'email' => 'tarik@e.mail'];
    }
}
