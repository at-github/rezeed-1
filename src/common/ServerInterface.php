<?php
namespace Common;

/**
 * ServerInterface
 *
 * @package default
 * @author Tarik
 */
interface ServerInterface {
    public function getUri(): string;
    public function getMethod(): string;
}
