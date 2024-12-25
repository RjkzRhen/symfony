<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('user/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }


    #[Route('/profile/update', name: 'app_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $user->setLastName($request->request->get('lastName'));
        $user->setFirstName($request->request->get('firstName'));
        $user->setMiddleName($request->request->get('middleName'));
        $user->setAge((int)$request->request->get('age'));
        $user->setUsername($request->request->get('username'));

        // Обработка номера телефона
        $phoneValue = $request->request->get('phone');
        $user->setPhoneValue($phoneValue);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Profile updated successfully.');

        return $this->redirectToRoute('app_profile');
    }

}