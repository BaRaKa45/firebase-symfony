<?php

namespace App\Service;

use App\Entity\User;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class Firebase
{
    private $serviceAccount;
    private $firebase;
    private $auth;
    private $token;

    /**
     *
     * add to service.yml :
     *
     * services:
     *      App\Service\Firebase:
     *           arguments:
     *                $root_dir: '%kernel.root_dir%'
     *
     * Firebase constructor.
     * @param $root_dir
     */
    public function __construct($root_dir)
    {
         try {
             $this->serviceAccount = ServiceAccount::fromJsonFile( $root_dir . '/../firebase.json');
         } catch (InvalidArgumentException $e) {
             echo('firebase.json can not be read');
         }

        $this->firebase = (new Factory())
            ->withServiceAccount($this->serviceAccount)
            ->create();
        $this->auth = $this->firebase->getAuth();
    }


    /**
     * Receive the token by javascript, POST
     *
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }


    /**
     * Get all the user informations
     * (exemple: $this->getUser()->email())
     *
     * @return \Kreait\Firebase\Auth\UserRecord
     */
    public function getUser()
    {
        $uid = $this->verifyToken($this->token)->getClaim('sub');
        $user = $this->firebase->getAuth()->getUser($uid);
        return $user;
    }

    /**
     * Delete an user
     *
     * @param string $uid
     */
    public function deleteUser(string $uid)
    {
        $this->firebase->getAuth()->deleteUser($uid);
    }

    /**
     * Check if a token is correct
     *
     * @param String $token
     * @return \Lcobucci\JWT\Token
     */
    private function verifyToken(String $token)
    {
        try {
            return $this->auth->verifyIdToken($token);
        } catch (\Firebase\Auth\Token\Exception\InvalidToken $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Authentication of the user
     *
     * @param User $user
     * @param string $firewall
     */
    public function authUser(User $user, string $firewall = "main")
    {
        $session = new Session();
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());;
        $session->set('_security_'. $firewall, serialize($token));
        $session->save();
    }
}