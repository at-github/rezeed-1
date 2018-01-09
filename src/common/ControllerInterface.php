<?php

namespace Common;

use Common\ResponseInterface;

interface ControllerInterface {
    const STATUS_CODE_INTERNAL_ERROR      = 500;
    const STATUS_CODE_RESSOURCE_NOT_FOUND = 404;
    const STATUS_CODE_UNPROCESSABLE       = 422;
    const STATUS_CODE_OK                  = 200;
    const STATUS_CODE_CREATED             = 201;

    public function setResponse(ResponseInterface $response): ControllerInterface;
}
