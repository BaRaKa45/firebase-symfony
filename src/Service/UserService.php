<?php

namespace App\Service;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class UserService
{

    private $serviceAccount;
    private $firebase;
    private $auth;

    public function __construct()
    {
        $this->serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/firebase.json');
        $this->firebase = (new Factory())
            ->withServiceAccount($this->serviceAccount)
            ->create();
        $this->auth = $this->firebase->getAuth();


    }

    public function getUser($token)
    {
        $uid = $this->verifyToken($token)->getClaim('sub');
        $user = $this->firebase->getAuth()->getUser($uid);
        return $user;
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function verifyToken($token)
    {
        try {
            return $this->auth->verifyIdToken($token);
        } catch (\Firebase\Auth\Token\Exception\InvalidToken $e) {
            echo $e->getMessage();
        }
    }
}