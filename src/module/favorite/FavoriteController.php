<?php

namespace Module\Favorite;

use Exception,
    RuntimeException;
use Common\{
        ResponseInterface,
        ControllerInterface
    };
use Module\{
        UserModel,
        SongModel
};

class FavoriteController implements ControllerInterface
{
    private $response;
    private $favoriteModel;
    private $userModel;
    private $songModel;

    public function setResponse(ResponseInterface $response): ControllerInterface
    {
        $this->response = $response;
        return $this;
    }

    public function setFavoriteModel($favoriteModel): ControllerInterface
    {
        $this->favoriteModel = $favoriteModel;
        return $this;
    }

    public function convertTimeForHuman(int $duration): string
    {
        $minutes  = intval($duration / 60);
        $secondes = $duration % 60;

        return "$minutes:$secondes";
    }

    public function getInfoFromId(int $id)
    {
        try {
            $favoriteInfo = $this->favoriteModel->getFavoriteSongFromUserId($id);
        } catch (RuntimeException $e){
            return $this->response->json(
                self::STATUS_CODE_INTERNAL_ERROR,
                [
                    'message' => $e->getMessage()
                ]
            );
        }

        if (is_null($favoriteInfo))
            return $this->response->json(
                self::STATUS_CODE_RESSOURCE_NOT_FOUND,
                ['message' => "no favorite for user id: $id"]
            );

        return $this->response->json(
            self::STATUS_CODE_OK,
            $favoriteInfo
        );
    }

    public function setUserModel($userModel): ControllerInterface
    {
        $this->userModel = $userModel;
        return $this;
    }

    public function setSongModel($songModel): ControllerInterface
    {
        $this->songModel = $songModel;
        return $this;
    }

    private function checkUserIdSongId(array $params)
    {
        if (!isset($params['user_id']))
            throw new Exception(
                'user_id is missing',
                self::STATUS_CODE_UNPROCESSABLE
            );

        if (!preg_match('/^\d*$/', $params['user_id']))
            throw new Exception(
                'user_id must be digit',
                self::STATUS_CODE_UNPROCESSABLE
            );

        if (!isset($params['song_id']))
            throw new Exception(
                'song_id is missing',
                self::STATUS_CODE_UNPROCESSABLE
            );

        if (!preg_match('/^\d*$/', $params['song_id']))
            throw new Exception(
                'song_id must be digit',
                self::STATUS_CODE_UNPROCESSABLE
            );
    }

    public function addSong(array $post)
    {
        try {
            $this->checkUserIdSongId($post);;
        } catch (Exception $e){
            return $this->response->json(
                $e->getCode(),
                ['message' => $e->getMessage()]
            );
        }

        $userId = intval($post['user_id'], 10);
        $songId = intval($post['song_id'], 10);

        //user exist ?
        if (is_null($this->userModel->getInfoFromId($userId)))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => "user with id: $userId doesn't exist"]
            );

        //song exist ?
        if (is_null($this->songModel->getInfoFromId($songId)))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => "song with id: $songId doesn't exist"]
            );

        try {
            $favoriteInfo = $this->favoriteModel->addSong($userId, $songId);
        } catch (RuntimeException $e){
            return $this->response->json(
                self::STATUS_CODE_INTERNAL_ERROR,
                [
                    'message' => $e->getMessage()
                ]
            );
        } catch (Exception $e){
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                [
                    'message' => $e->getMessage()
                ]
            );
        }

        return $this->response->json(
            self::STATUS_CODE_CREATED,
            $favoriteInfo
        );
    }

    public function deleteSong($delete)
    {
        try {
            $this->checkUserIdSongId($delete);;
        } catch (Exception $e){
            return $this->response->json(
                $e->getCode(),
                ['message' => $e->getMessage()]
            );
        }

        $userId = intval($delete['user_id'], 10);
        $songId = intval($delete['song_id'], 10);

        //user exist ?
        if (is_null($this->userModel->getInfoFromId($userId)))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => "user with id: $userId doesn't exist"]
            );

        //song exist ?
        if (is_null($this->songModel->getInfoFromId($songId)))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => "song with id: $songId doesn't exist"]
            );

        try {
            $favoriteInfo = $this->favoriteModel->deleteSong($userId, $songId);
        } catch (RuntimeException $e){
            return $this->response->json(
                self::STATUS_CODE_INTERNAL_ERROR,
                [
                    'message' => $e->getMessage()
                ]
            );
        } catch (Exception $e){
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                [
                    'message' => $e->getMessage()
                ]
            );
        }

        if (is_null($favoriteInfo))
            return $this->response->json(
                self::STATUS_CODE_RESSOURCE_NOT_FOUND,
                ['message' => "no favorite for user id: $userId"]
            );

        return $this->response->json(
            self::STATUS_CODE_OK,
            $favoriteInfo
        );
    }
}
