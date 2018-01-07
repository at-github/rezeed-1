<?php

namespace Module\User;

use RuntimeException;
use Common\{
        ResponseInterface,
        ControllerInterface
    };

class UserController implements ControllerInterface
{
    private $response;
    private $userModel;

    public function setResponse(ResponseInterface $response): ControllerInterface
    {
        $this->response = $response;
        return $this;
    }

    public function setUserModel($userModel): ControllerInterface
    {
        $this->userModel = $userModel;
        return $this;
    }

    public function getInfoFromId(int $id)
    {
        try {
            $userInfo = $this->userModel->getInfoFromId($id);;
        } catch (RuntimeException $e){
            return $this->response->json(
                self::STATUS_CODE_INTERNAL_ERROR,
                [
                    'message' => $e->getMessage()
                ]
            );
        }

        if (is_null($userInfo))
            return $this->response->json(
                self::STATUS_CODE_RESSOURCE_NOT_FOUND,
                ['message' => "no user with id: $id"]
            );

        return $this->response->json(
            self::STATUS_CODE_OK,
            $userInfo
        );
    }
}
