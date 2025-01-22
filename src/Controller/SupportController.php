<?php

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
    // Определяет маршрут для страницы поддержки
    #[Route('/support', name: 'app_support')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь аутентифицирован
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Получает текущего пользователя
        $user = $this->getUser();

        // Создает новый объект сообщения поддержки
        $supportMessage = new SupportMessage();
        $supportMessage->setUser($user); // Устанавливает пользователя для сообщения

        // Создает форму для отправки сообщения поддержки
        $form = $this->createForm(SupportMessageType::class, $supportMessage);
        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет сообщение
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($supportMessage); // Подготавливает объект для сохранения
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Добавляет сообщение об успешной отправке сообщения
            $this->addFlash('success', 'Ваше сообщение отправлено в службу поддержки.');
            return $this->redirectToRoute('app_support'); // Перенаправляет на страницу поддержки
        }

        // Отображает страницу поддержки
        return $this->render('support/index.html.twig', [
            'form' => $form->createView(), // Форма для отправки сообщения поддержки
        ]);
    }

    // Определяет маршрут для страницы поддержки администратора
    #[Route('/admin/support', name: 'app_admin_support')]
    public function adminSupport(EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Получает все сообщения поддержки
        $messages = $entityManager->getRepository(SupportMessage::class)->findAll();

        // Отображает страницу поддержки администратора
        return $this->render('support/admin.html.twig', [
            'messages' => $messages, // Список сообщений поддержки
        ]);
    }

    // Определяет маршрут для ответа на сообщение поддержки
    #[Route('/admin/support/{id}/reply', name: 'app_admin_support_reply', methods: ['POST'])]
    public function replyToMessage(Request $request, SupportMessage $supportMessage, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Получает ответ из формы
        $response = $request->request->get('response');
        $supportMessage->setResponse($response); // Устанавливает ответ на сообщение
        $entityManager->flush(); // Сохраняет изменения в базе данных

        // Добавляет сообщение об успешной отправке ответа
        $this->addFlash('success', 'Ответ отправлен.');
        return $this->redirectToRoute('app_admin_support'); // Перенаправляет на страницу поддержки администратора
    }
}