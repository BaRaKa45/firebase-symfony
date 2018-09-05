<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
    public function register(Request $request, UserService $userService, TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager)
    {
        $token = $request->request->get('token');

        if(!empty($token)) {
            $uid = $userService->getUser($token)->uid;
            $userRepository = $this->getDoctrine()->getRepository(User::class);

            if($userRepository->findOneBy(['uid'=> $uid]) == null) {
                $user = new User();
                $user->setUid($uid);
                $user->setEmail($userService->getUser($token)->email);
                $user->setPassword('test');
                $user->setIsActive(true);
                $user->setUsername($userService->getUser($token)->email);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                //-------------------------------------



                $token_ = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->container->get('security.token_storage')->setToken(($token_));
                $this->get('session')->set('main', serialize($token_));

                //------------------------------------------
                return $this->render('home/index.html.twig', [
                    'user' => $user,
                ]);
            }
        }
        return $this->redirectToRoute('home');
    }
}
