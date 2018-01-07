<?php

namespace Common;

use Common\ResponseInterface;

//todo make an abstract class
interface ControllerInterface {
    const STATUS_CODE_INTERNAL_ERROR      = 500;
    const STATUS_CODE_RESSOURCE_NOT_FOUND = 404;
    const STATUS_CODE_OK                  = 200;

    public function setResponse(ResponseInterface $response): ControllerInterface;
}
