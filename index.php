<?php
//TODO move this file in public !!!!
ini_set('display_errors', true);
require_once 'vendor/autoload.php';

use Common\{
        Server,
        Router
    };

(new Router())
    ->setServer(new Server())
    ->dispatch();
