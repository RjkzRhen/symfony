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
    // Определяет маршрут для страницы чата
    #[Route('/chat', name: 'app_chat')]
    public function index(Request $request, EntityManagerInterface $entityManager, ChatRepository $chatRepository): Response
    {
        // Получает текущего пользователя
        $user = $this->getUser();

        // Получает или создает чат для пользователя
        $chat = $chatRepository->findChatByUser($user->getId());
        if (!$chat) {
            $chat = new Chat(); // Создает новый чат
            $chat->setUser($user); // Устанавливает пользователя для чата
            $chat->setCreatedAt(new \DateTime()); // Устанавливает текущее время создания чата
            $entityManager->persist($chat); // Подготавливает чат для сохранения
            $entityManager->flush(); // Сохраняет чат в базе данных
        }

        // Получает все сообщения для этого чата
        $messages = $chat->getMessages();

        // Создает форму для отправки нового сообщения
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет сообщение
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setChat($chat); // Устанавливает чат для сообщения
            $message->setSender($user); // Устанавливает отправителя (текущего пользователя)
            $message->setCreatedAt(new \DateTime()); // Устанавливает текущее время создания сообщения
            $entityManager->persist($message); // Подготавливает сообщение для сохранения
            $entityManager->flush(); // Сохраняет сообщение в базе данных

            // Если это AJAX-запрос, возвращает JSON-ответ
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }

            // Перенаправляет на страницу чата
            return $this->redirectToRoute('app_chat');
        }

        // Отображает страницу чата
        return $this->render('chat/index.html.twig', [
            'messages' => $messages, // Сообщения текущего чата
            'form' => $form->createView(), // Форма для отправки сообщения
        ]);
    }
}