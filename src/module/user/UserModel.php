<?php

namespace Module\User;

use Exception,
    RuntimeException,
    PDO;

class UserModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getInfoFromId(int $id)
    {
        $id = intval($id, 10);

        $query = "SELECT id, name, email FROM user WHERE id=$id;";

        try {
            $result = $this->db->query($query);
        } catch (Exception $e){
            throw new RuntimeException(
                'Unable to execute query',
                intval($e->getCode(), 10),
                $e
            );
        }

        $resultFetched = $result->fetch(PDO::FETCH_OBJ);

        if (empty($resultFetched))
            return null;

        return [
            'id'    => $resultFetched->id,
            'name'  => $resultFetched->name,
            'email' => $resultFetched->email
        ];
    }
}
