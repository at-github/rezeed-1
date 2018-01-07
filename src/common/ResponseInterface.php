<?php

namespace Common;

interface ResponseInterface {
    public function json(int $httpCode, array $body);
}
