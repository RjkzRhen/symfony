<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        // Проверяем, что пользователь аутентифицирован
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Получаем текущего пользователя
        $user = $this->getUser();

        // Отображаем страницу профиля
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/update', name: 'app_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        // Проверяем, что пользователь аутентифицирован
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Получаем текущего пользователя
        $user = $this->getUser();

        // Обновляем данные пользователя
        $user->setLastName($request->request->get('lastName'));
        $user->setFirstName($request->request->get('firstName'));
        $user->setMiddleName($request->request->get('middleName'));
        $user->setAge((int)$request->request->get('age'));
        $user->setUsername($request->request->get('username'));
        $user->setEmail($request->request->get('email'));

        // Обработка номера телефона
        $phoneValue = $request->request->get('phone');
        $user->setPhoneValue($phoneValue);

        // Обработка метода 2FA
        $twoFactorMethod = $request->request->get('twoFactorMethod');
        $user->setTwoFactorMethod($twoFactorMethod);

        // Обработка Telegram ID, если выбран метод Telegram
        if ($twoFactorMethod === 'telegram') {
            $telegramId = $request->request->get('telegramId');
            $user->setTelegramId($telegramId);
        } else {
            $user->setTelegramId(null); // Очищаем Telegram ID, если выбран другой метод
        }

        // Валидация данных пользователя
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            // Если есть ошибки валидации, добавляем их в flash-сообщения
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            // Перенаправляем пользователя обратно на страницу профиля
            return $this->redirectToRoute('app_profile');
        }

        // Сохраняем изменения в базе данных
        $entityManager->persist($user);
        $entityManager->flush();

        // Добавляем сообщение об успешном обновлении профиля
        $this->addFlash('success', 'Profile updated successfully.');

        // Перенаправляем пользователя на страницу профиля
        return $this->redirectToRoute('app_profile');
    }
}