<?php

namespace Common;

/**
 * ResponseInterface
 *
 * @package default
 * @author Tarik
 */
interface ResponseInterface {
    public function json(int $httpCode, array $body);
}
