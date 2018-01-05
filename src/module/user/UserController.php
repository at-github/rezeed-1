<?php

namespace Module\User;

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
        $userInfo = $this->userModel->getInfoFromId($id);

        return $this->response->json(200, $userInfo);
    }
}
