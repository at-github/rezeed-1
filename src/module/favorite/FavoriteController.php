<?php

namespace Module\Favorite;

use RuntimeException;
use Common\{
        ResponseInterface,
        ControllerInterface
    };

class FavoriteController implements ControllerInterface
{
    private $response;
    private $favoriteModel;

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
            $favoriteInfo = $this->favoriteModel->getFavoriteSongFromUserId($id);;
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
}
