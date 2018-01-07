<?php

namespace Module\Favorite;

use Exception,
    RuntimeException,
    PDO;

class FavoriteModel
{
    const TABLE_NAME = 'favorite_song';

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getFavoriteSongFromUserId(int $id)
    {
        $id = intval($id, 10);

        // SELECT title FROM song
        //   INNER JOIN favorite_song ON song.id = favorite_song.song_id
        //   INNER JOIN user ON user.id = favorite_song.user_id
        //   WHERE user.id = 1;`
        $query = 'SELECT title FROM ' . \Module\Song\SongModel::TABLE_NAME
                . ' INNER JOIN ' . self::TABLE_NAME
                    . ' ON ' . \Module\Song\SongModel::TABLE_NAME . '.id = ' . self::TABLE_NAME . '.song_id'
                . ' INNER JOIN ' . \Module\User\UserModel::TABLE_NAME
                    . ' ON ' . \Module\User\UserModel::TABLE_NAME . '.id = ' . self::TABLE_NAME . '.user_id'
                . ' WHERE ' . \Module\User\UserModel::TABLE_NAME . ".id = $id;";

        try {
            $result = $this->db->query($query);
        } catch (Exception $e){
            throw new RuntimeException(
                'Unable to execute query',
                intval($e->getCode(), 10),
                $e
            );
        }

        $resultFetched = $result->fetchAll(PDO::FETCH_OBJ); //?

        if (empty($resultFetched))
            return null;

        return $resultFetched;
    }
}
