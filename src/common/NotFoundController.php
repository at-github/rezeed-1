<?php

namespace Common;

use Common\Response,
    Common\ResponseInterface;

class NotFoundController
{
    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function response()
    {
        return $this->response->json(404, 'Route doesn\'t exist');
    }
}
