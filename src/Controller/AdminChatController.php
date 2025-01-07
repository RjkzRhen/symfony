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
    #[Route('/admin/chat', name: 'app_admin_chat')]
    public function index(ChatRepository $chatRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Получаем все чаты
        $chats = $chatRepository->findAllChatsForAdmin();

        // Если есть чаты, перенаправляем на первый чат
        if (!empty($chats)) {
            return $this->redirectToRoute('app_admin_chat_view', ['id' => $chats[0]->getId()]);
        }

        // Если чатов нет, показываем пустую страницу
        return $this->render('admin/chat/index.html.twig', [
            'chats' => $chats,
        ]);
    }

    #[Route('/admin/chat/{id}', name: 'app_admin_chat_view', methods: ['GET', 'POST'])]
    public function viewChat(Chat $chat, Request $request, EntityManagerInterface $entityManager, ChatRepository $chatRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Получаем все чаты для отображения списка
        $chats = $chatRepository->findAllChatsForAdmin();

        // Получаем все сообщения для текущего чата
        $messages = $chat->getMessages();

        // Форма для отправки нового сообщения
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setChat($chat);
            $message->setSender($this->getUser()); // Админ отправляет сообщение
            $message->setCreatedAt(new \DateTime());
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_chat_view', ['id' => $chat->getId()]);
        }

        return $this->render('admin/chat/view.html.twig', [
            'chats' => $chats, // Список всех чатов
            'currentChat' => $chat, // Текущий выбранный чат
            'messages' => $messages, // Сообщения текущего чата
            'form' => $form->createView(), // Форма для отправки сообщения
        ]);
    }
}