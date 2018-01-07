<?php

namespace Module\Song;

use Exception,
    RuntimeException,
    PDO;

class SongModel
{
    const TABLE_NAME = 'song';

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getInfoFromId(int $id)
    {
        $id = intval($id, 10);

        $query = 'SELECT id, title, duration FROM ' . self::TABLE_NAME . " WHERE id=$id;";

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
            'id'       => $resultFetched->id,
            'title'    => $resultFetched->title,
            'duration' => $resultFetched->duration
        ];
    }
}
