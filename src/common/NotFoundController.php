<?php

namespace Common;

use Common\{
    ResponseInterface,
    ControllerInterface
};

class NotFoundController implements ControllerInterface
{
    public function setResponse(ResponseInterface $response): ControllerInterface
    {
        $this->response = $response;
        return $this;
    }

    public function response()
    {
        return $this->response->json(
            self::STATUS_CODE_RESSOURCE_NOT_FOUND,
            ["message" => 'Route doesn\'t exist']
        );
    }
}
