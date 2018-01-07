<?php

namespace Module\Song;

use RuntimeException;
use Common\{
        ResponseInterface,
        ControllerInterface
    };

class SongController implements ControllerInterface
{
    private $response;
    private $songModel;

    public function setResponse(ResponseInterface $response): ControllerInterface
    {
        $this->response = $response;
        return $this;
    }

    public function setSongModel($songModel): ControllerInterface
    {
        $this->songModel = $songModel;
        return $this;
    }

    private function convertTimeForHuman(int $duration): string
    {
        $minutes  = intval($duration / 60);
        $secondes = $duration % 60;

        return "$minutes:$secondes";
    }

    public function getInfoFromId(int $id)
    {
        try {
            $songInfo = $this->songModel->getInfoFromId($id);;
        } catch (RuntimeException $e){
            return $this->response->json(
                self::STATUS_CODE_INTERNAL_ERROR,
                [
                    'message' => $e->getMessage()
                ]
            );
        }

        if (is_null($songInfo))
            return $this->response->json(
                self::STATUS_CODE_RESSOURCE_NOT_FOUND,
                ['message' => "no song with id: $id"]
            );

        $songInfo['duration'] = $this->convertTimeForHuman($songInfo['duration']);

        return $this->response->json(
            self::STATUS_CODE_OK,
            $songInfo
        );
    }
}
