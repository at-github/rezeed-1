<?php

namespace Common;

use Common\ResponseInterface;

//todo make an abstract class
interface ControllerInterface {
    public function setResponse(ResponseInterface $response): ControllerInterface;
}
