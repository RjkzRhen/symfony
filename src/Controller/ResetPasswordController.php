<?php
// src/Controller/ResetPasswordController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    // Определяет маршрут для запроса сброса пароля
    #[Route('/reset-password', name: 'app_reset_password_request')]
    public function request(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        // Создает форму для запроса сброса пароля
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, обрабатывает запрос
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); // Получает данные из формы
            $username = $data['username']; // Получает имя пользователя
            $email = $data['email']; // Получает email

            // Находит пользователя по имени пользователя и email
            $user = $userRepository->findOneBy(['username' => $username, 'email' => $email]);

            if ($user) {
                // Генерирует случайный 6-значный код
                $code = random_int(100000, 999999);
                $user->setResetPasswordCode($code); // Устанавливает код для сброса пароля
                $user->setResetPasswordCodeExpiry((new \DateTime())->modify('+1 hour')); // Устанавливает срок действия кода

                $entityManager->flush(); // Сохраняет изменения в базе данных

                // Отправляет письмо с кодом для сброса пароля
                $email = (new Email())
                    ->from('noreply@example.com') // Отправитель
                    ->to($user->getEmail()) // Получатель
                    ->subject('Восстановление пароля') // Тема
                    ->text(sprintf('Ваш код для восстановления пароля: %s', $code)); // Текст

                $mailer->send($email); // Отправляет письмо

                // Добавляет сообщение об успешной отправке кода
                $this->addFlash('success', 'Код для восстановления пароля отправлен на вашу почту.');
                return $this->redirectToRoute('app_reset_password_verify'); // Перенаправляет на страницу ввода кода
            } else {
                // Если пользователь не найден, добавляет сообщение об ошибке
                $this->addFlash('error', 'Пользователь с таким логином и почтой не найден.');
            }
        }

        // Отображает страницу запроса сброса пароля
        return $this->render('reset_password/request.html.twig', [
            'form' => $form->createView(), // Форма для запроса сброса пароля
        ]);
    }

    // Определяет маршрут для проверки кода сброса пароля
    #[Route('/reset-password/verify', name: 'app_reset_password_verify')]
    public function verify(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Обрабатывает POST-запрос для проверки кода
        if ($request->isMethod('POST')) {
            $code = $request->request->get('code'); // Получает код из формы
            $newPassword = $request->request->get('newPassword'); // Получает новый пароль из формы

            // Находит пользователя по коду сброса пароля
            $user = $userRepository->findOneBy(['resetPasswordCode' => $code]);

            // Проверяет код и его срок действия
            if ($user && $user->getResetPasswordCodeExpiry() > new \DateTime()) {
                // Хеширует новый пароль
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $user->setResetPasswordCode(null); // Очищает код сброса пароля
                $user->setResetPasswordCodeExpiry(null); // Очищает срок действия кода

                $entityManager->flush(); // Сохраняет изменения в базе данных

                // Добавляет сообщение об успешном изменении пароля
                $this->addFlash('success', 'Пароль успешно изменен.');
                return $this->redirectToRoute('app_login'); // Перенаправляет на страницу входа
            } else {
                // Если код неверный или истек, добавляет сообщение об ошибке
                $this->addFlash('error', 'Неверный или истекший код.');
            }
        }

        // Отображает страницу ввода кода для сброса пароля
        return $this->render('reset_password/verify.html.twig');
    }
}