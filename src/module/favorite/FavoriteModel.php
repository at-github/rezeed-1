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

        /** SELECT title FROM song
         *      INNER JOIN favorite_song ON song.id = favorite_song.song_id
         *      INNER JOIN user ON user.id = favorite_song.user_id
         *   WHERE user.id = 1;
         */
        $query = 'SELECT song.id, song.title FROM ' . \Module\Song\SongModel::TABLE_NAME
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

        $resultFetched = $result->fetchAll(PDO::FETCH_OBJ);

        if (empty($resultFetched))
            return null;

        return $resultFetched;
    }

    public function addSong(int $userId, int $songId)
    {
        // Already in favorites user ?
        $querySelect = 'SELECT count(*) FROM ' . self::TABLE_NAME . " WHERE user_id = $userId AND song_id = $songId;";
        try {
            $resultFetched = $this->db->query($querySelect)
                                      ->fetch(PDO::FETCH_COLUMN);
        } catch (Exception $e){
            throw new RuntimeException(
                'Unable to execute query',
                intval($e->getCode(), 10),
                $e
            );
        }

        if ($resultFetched != 0)
            throw new Exception("song with id: $songId already in favorite of user: $userId");

        try {
            $queryInsert = 'INSERT INTO ' . self::TABLE_NAME . " (user_id, song_id) VALUES ($userId, $songId)";
            $this->db->query($queryInsert);
        } catch (Exception $e){
            throw new RuntimeException(
                // TODO const
                'Unable to execute query',
                intval($e->getCode(), 10),
                $e
            );
        }

        return $this->getFavoriteSongFromUserId($userId);
    }

    public function deleteSong(int $userId, int $songId)
    {
        $querySelect = 'SELECT count(*) FROM ' . self::TABLE_NAME . " WHERE user_id = $userId AND song_id = $songId;";

        try {
            $result = $this->db->query($querySelect);
        } catch (Exception $e){
            throw new RuntimeException(
                'Unable to execute query',
                intval($e->getCode(), 10),
                $e
            );
        }

        $resultFetched = intval($result->fetch(PDO::FETCH_COLUMN));
        if ($resultFetched === 0)
            throw new Exception("no song with id: $songId in favorite of user: $userId");

        $queryDelete = 'DELETE FROM ' . self::TABLE_NAME . " WHERE user_id = $userId AND song_id = $songId;";

        try {
            $this->db->query($queryDelete);
        } catch (Exception $e){
            throw new RuntimeException(
                'Unable to execute query',
                intval($e->getCode(), 10),
                $e
            );
        }

        return $this->getFavoriteSongFromUserId($userId);
    }
}
