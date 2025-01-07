<?php

// src/Controller/SupportController.php
namespace App\Controller;

use App\Entity\SupportMessage;
use App\Form\SupportMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupportController extends AbstractController
{
    #[Route('/support', name: 'app_support')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $supportMessage = new SupportMessage();
        $supportMessage->setUser($user);

        $form = $this->createForm(SupportMessageType::class, $supportMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($supportMessage);
            $entityManager->flush();

            $this->addFlash('success', 'Ваше сообщение отправлено в службу поддержки.');
            return $this->redirectToRoute('app_support');
        }

        return $this->render('support/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/support', name: 'app_admin_support')]
    public function adminSupport(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $messages = $entityManager->getRepository(SupportMessage::class)->findAll();

        return $this->render('support/admin.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/admin/support/{id}/reply', name: 'app_admin_support_reply', methods: ['POST'])]
    public function replyToMessage(Request $request, SupportMessage $supportMessage, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $response = $request->request->get('response');
        $supportMessage->setResponse($response);
        $entityManager->flush();

        $this->addFlash('success', 'Ответ отправлен.');
        return $this->redirectToRoute('app_admin_support');
    }
}