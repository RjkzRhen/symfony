<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(Request $request, EntityManagerInterface $entityManager, ChatRepository $chatRepository): Response
    {
        $user = $this->getUser();

        // Получаем или создаем чат для пользователя
        $chat = $chatRepository->findChatByUser($user->getId());
        if (!$chat) {
            $chat = new Chat();
            $chat->setUser($user);
            $chat->setCreatedAt(new \DateTime());
            $entityManager->persist($chat);
            $entityManager->flush();
        }

        // Получаем все сообщения для этого чата
        $messages = $chat->getMessages();

        // Форма для отправки нового сообщения
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setChat($chat);
            $message->setSender($user);
            $message->setCreatedAt(new \DateTime());
            $entityManager->persist($message);
            $entityManager->flush();

            // Если это AJAX-запрос, возвращаем JSON-ответ
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }

            return $this->redirectToRoute('app_chat');
        }

        return $this->render('chat/index.html.twig', [
            'messages' => $messages,
            'form' => $form->createView(),
        ]);
    }
}