<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Firebase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth", name="auth")
     */
    public function index()
    {

        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    /**
     * @Route("/register", name="register", methods="POST")
     */
    public function register(Request $request, Firebase $firebase, UserPasswordEncoderInterface $encoder)
    {
        $token = $request->request->get('token');

        if (!empty($token)) {
            $firebase->setToken($token);
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $userInDataBase = $userRepository->findOneBy(['uid' => $firebase->getUser()->uid]);
            if ($userInDataBase == null) {

                $user = new User();
                $user->setUid($firebase->getUser()->uid);
                $user->setEmail($firebase->getUser()->email);
                $user->setName($firebase->getUser()->displayName);
                $encoded = $encoder->encodePassword($user, $firebase->getUser()->uid);
                $user->setPassword($encoded);
                $user->setIsActive(true);

                if ($firebase->getUser()->email != null) {
                    $user->setUsername($firebase->getUser()->email);
                } else if ($firebase->getUser($token)->phoneNumber != null) {
                    $user->setUsername($firebase->getUser()->phoneNumber);
                }

                $user->setPicture($firebase->getUser()->photoUrl);

                if (count($userRepository->findAll()) == 0) {
                    $user->setRoles(['ROLE_ADMIN']);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $firebase->authUser($user);

                return $this->redirectToRoute('home', [
                    'user' => $user,
                ]);
            } else {
                $firebase->authUser($userInDataBase);
            }
        }
        return $this->redirectToRoute('home');
    }


    /**
     * @Route("disconnect", name="disconnect", methods="GET")
     */
    public function disconnect()
    {
        $this->redirectToRoute('home');
    }

    /**
     * @Route("profil", name="profil", methods="GET")
     */
    public function profil()
    {
        return $this->render('auth/profil.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
