<?php

namespace Common;

use Common\{
    Response,
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
        return $this->response->json(404, ["message" => 'Route doesn\'t exist']);
    }
}
