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
use Symfony\Component\Routing\Annotation\Route;

class AdminChatController extends AbstractController
{
    // Определяет маршрут для страницы администрирования чатов
    #[Route('/admin/chat', name: 'app_admin_chat')]
    public function index(ChatRepository $chatRepository): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Получает все чаты для администратора
        $chats = $chatRepository->findAllChatsForAdmin();

        // Если есть чаты, перенаправляет на первый чат
        if (!empty($chats)) {
            return $this->redirectToRoute('app_admin_chat_view', ['id' => $chats[0]->getId()]);
        }

        // Если чатов нет, отображает пустую страницу
        return $this->render('admin/chat/index.html.twig', [
            'chats' => $chats, // Список всех чатов
        ]);
    }

    // Определяет маршрут для просмотра конкретного чата
    #[Route('/admin/chat/{id}', name: 'app_admin_chat_view', methods: ['GET', 'POST'])]
    public function viewChat(Chat $chat, Request $request, EntityManagerInterface $entityManager, ChatRepository $chatRepository): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Получает все чаты для отображения списка
        $chats = $chatRepository->findAllChatsForAdmin();

        // Получает все сообщения для текущего чата
        $messages = $chat->getMessages();

        // Создает форму для отправки нового сообщения
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        // Если форма отправлена и валидна, сохраняет сообщение
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setChat($chat); // Устанавливает чат для сообщения
            $message->setSender($this->getUser()); // Устанавливает отправителя (админа)
            $message->setCreatedAt(new \DateTime()); // Устанавливает текущее время создания сообщения
            $entityManager->persist($message); // Подготавливает сообщение для сохранения
            $entityManager->flush(); // Сохраняет сообщение в базе данных

            // Перенаправляет на страницу просмотра текущего чата
            return $this->redirectToRoute('app_admin_chat_view', ['id' => $chat->getId()]);
        }

        // Отображает страницу просмотра чата
        return $this->render('admin/chat/view.html.twig', [
            'chats' => $chats, // Список всех чатов
            'currentChat' => $chat, // Текущий выбранный чат
            'messages' => $messages, // Сообщения текущего чата
            'form' => $form->createView(), // Форма для отправки сообщения
        ]);
    }
}
