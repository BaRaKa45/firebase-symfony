<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Firebase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/users", name="users_listing")
     */
    public function userListing()
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/user/edit/{id}", name="user_edit")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userEdit($id)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        return $this->render('admin/user.html.twig', [
            'user' => $userRepository->find($id),
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", name="user_delete")
     * @param $id
     * @param Firebase $userService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function userDelete($id, Firebase $userService)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($id);
        $em->remove($user);
        $em->flush();
        $userService->deleteUser($user->getUid());

        return $this->redirectToRoute('users_listing', ['users' => $userRepository->findAll()]);
    }
}
