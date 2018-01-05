<?php

namespace Common;

class Response implements ResponseInterface
{
    public function json(int $httpCode, string $message)
    {
        //Define message from status
        switch($httpCode) {
            case 404:
                $status = $_SERVER['SERVER_PROTOCOL'] . " $httpCode Not Found";
                break;
            default:
                $status = $_SERVER['SERVER_PROTOCOL'] . ' ' . $httpCode;
                break;
        }

        header($status);
        header('Content-Type: application/json');

        echo json_encode(
            compact('message'),
            true
        );
    }
}
