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

    public function addSong(array $post)
    {
        if (!isset($post['user_id']))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => 'user_id is missing']
            );

        if (!preg_match('/^\d*$/', $post['user_id']))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => 'user_id must be digit']
            );

        if (!isset($post['song_id']))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => 'song_id is missing']
            );

        if (!preg_match('/^\d*$/', $post['song_id']))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => 'song_id must be digit']
            );

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
        if (!isset($delete['user_id']))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => 'user_id is missing']
            );

        if (!preg_match('/^\d*$/', $delete['user_id']))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => 'user_id must be digit']
            );

        if (!isset($delete['song_id']))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => 'song_id is missing']
            );

        if (!preg_match('/^\d*$/', $delete['song_id']))
            return $this->response->json(
                self::STATUS_CODE_UNPROCESSABLE,
                ['message' => 'song_id must be digit']
            );

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
