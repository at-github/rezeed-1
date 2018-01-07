<?php

namespace Common;

use Exception;
use Common\{
    NotFoundController,
    Response
};
use Module\{
    User\UserModel,
    User\UserController,
    Song\SongController,
    Song\SongModel,
    Favorite\FavoriteController,
    Favorite\FavoriteModel
};

class Router
{
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_DELETE = 'DELETE';

    private $server;

    public function setServer(ServerInterface $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function dispatch()
    {
        $uri      = $this->server->getUri();
        $method   = $this->server->getMethod();
        $post     = $this->server->getPost();
        $response = new Response();

        try {
            $pdo = DbConnect::getInstance()->getPdo();;
        } catch (Exception $e){
            $response->json(
                ControllerInterface::STATUS_CODE_INTERNAL_ERROR,
                ['message' => 'can\'t connect to database']
            );
            die();
        }

        // route: GET /user/:id
        if (
            $method === self::METHOD_GET &&
            preg_match('/^\/user\/(\d+)$/', $uri, $slugs)
        ) {
            (new UserController())
                ->setResponse($response)
                ->setUserModel(new UserModel($pdo))
                ->getInfoFromId($slugs[1]);
        // route: GET /song/:id
        } else if (
            $method === self::METHOD_GET &&
            preg_match('/^\/song\/(\d+)$/', $uri, $slugs)
        ) {
            (new SongController())
                ->setResponse($response)
                ->setSongModel(new SongModel($pdo))
                ->getInfoFromId($slugs[1]);
        // route: GET /favorite/user/:id
        } else if (
            $method === self::METHOD_GET &&
            preg_match('/^\/favorite\/user\/(\d+)$/', $uri, $slugs)
        ) {
            (new FavoriteController())
                ->setResponse($response)
                ->setFavoriteModel(new FavoriteModel($pdo))
                ->getInfoFromId($slugs[1]);
        // route: POST /favorite/
        // body user_id song_id
        } else if (
            $method === self::METHOD_POST &&
            preg_match('/^\/favorite$/', $uri, $slugs)
        ) {
            (new FavoriteController())
                ->setResponse($response)
                ->setFavoriteModel(new FavoriteModel($pdo))
                ->setUserModel(new UserModel($pdo))
                ->setSongModel(new SongModel($pdo))
                ->addSong($this->server->getPost());
        // route: DELETE /favorite/song/:id
        // body user_id song_id
        } else if (
            $method === self::METHOD_DELETE &&
            preg_match('/^\/favorite$/', $uri, $slugs)
        ) {
            (new FavoriteController())
                ->setResponse($response)
                ->setFavoriteModel(new FavoriteModel($pdo))
                ->setUserModel(new UserModel($pdo))
                ->setSongModel(new SongModel($pdo))
                ->deleteSong($this->server->getDelete());
        // 404
        } else {
            (new NotFoundController())
                ->setResponse($response)
                ->response();
        }
    }
}
