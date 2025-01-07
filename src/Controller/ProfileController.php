<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TwoFactorAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request, TwoFactorAuthService $twoFactorAuthService, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if ($user->isTwoFactorEnabled()) {
            // Проверяем, был ли уже отправлен код
            if (!$request->getSession()->has('2fa_user')) {
                $code = random_int(100000, 999999);
                $user->setTwoFactorCode($code);
                $user->setTwoFactorCodeExpiry((new \DateTime())->modify('+5 minutes'));

                $method = $user->getTwoFactorMethod();
                if ($method === 'email') {
                    $twoFactorAuthService->sendEmailCode($user->getEmail(), $code);
                } elseif ($method === 'telegram') {
                    $twoFactorAuthService->sendTelegramCode($user->getTelegramId(), $code);
                }

                $entityManager->flush();

                // Сохраняем имя пользователя в сессии для процесса 2FA
                $request->getSession()->set('2fa_user', $user->getUsername());

                return $this->redirectToRoute('app_2fa');
            }
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/2fa-settings', name: 'app_2fa_settings', methods: ['GET', 'POST'])]
    public function twoFactorSettings(Request $request, EntityManagerInterface $entityManager, TwoFactorAuthService $twoFactorAuthService): Response
    {
        // Проверяем, что пользователь аутентифицирован
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Получаем текущего пользователя
        $user = $this->getUser();

        // Проверяем, что объект пользователя не равен null
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found.');
        }

        // Обработка формы
        if ($request->isMethod('POST')) {
            $isTwoFactorEnabled = $request->request->get('isTwoFactorEnabled') === 'on';
            $twoFactorMethod = $request->request->get('twoFactorMethod');

            // Обновляем настройки 2FA
            $user->setIsTwoFactorEnabled($isTwoFactorEnabled);
            $user->setTwoFactorMethod($twoFactorMethod);

            // Если выбран метод Telegram, сохраняем Telegram ID
            if ($twoFactorMethod === 'telegram') {
                $telegramId = $request->request->get('telegramId');
                $user->setTelegramId($telegramId);
            } else {
                $user->setTelegramId(null); // Очищаем Telegram ID, если выбран другой метод
            }

            // Сохраняем изменения в базе данных
            $entityManager->persist($user);
            $entityManager->flush();

            // Добавляем сообщение об успешном обновлении
            $this->addFlash('success', '2FA settings updated successfully.');
            return $this->redirectToRoute('app_2fa_settings');
        }

        // Отображаем страницу настроек 2FA
        return $this->render('user/2fa_settings.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/send-2fa-code', name: 'app_send_2fa_code', methods: ['POST'])]
    public function sendTwoFactorCode(Request $request, EntityManagerInterface $entityManager, TwoFactorAuthService $twoFactorAuthService): Response
    {
        // Проверяем, что пользователь аутентифицирован
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Получаем текущего пользователя
        $user = $this->getUser();

        // Проверяем, что объект пользователя не равен null
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found.');
        }

        // Генерация случайного 6-значного кода
        $code = random_int(100000, 999999);
        $user->setTwoFactorCode($code);
        $user->setTwoFactorCodeExpiry((new \DateTime())->modify('+5 minutes')); // Код действителен 5 минут

        // Отправка кода на выбранный метод
        $twoFactorMethod = $user->getTwoFactorMethod();
        if ($twoFactorMethod === 'email') {
            $twoFactorAuthService->sendEmailCode($user->getEmail(), $code);
        } elseif ($twoFactorMethod === 'telegram') {
            $twoFactorAuthService->sendTelegramCode($user->getTelegramId(), $code);
        }

        // Сохраняем изменения в базе данных
        $entityManager->persist($user);
        $entityManager->flush();

        // Перенаправляем на страницу ввода кода
        return $this->redirectToRoute('app_verify_2fa_code');
    }

    #[Route('/profile/verify-2fa-code', name: 'app_verify_2fa_code', methods: ['GET', 'POST'])]
    public function verifyTwoFactorCode(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Проверяем, что пользователь аутентифицирован
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Получаем текущего пользователя
        $user = $this->getUser();

        // Проверяем, что объект пользователя не равен null
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found.');
        }

        // Обработка ввода кода
        if ($request->isMethod('POST')) {
            $code = $request->request->get('code');

            // Проверка кода и его срока действия
            if ($code === $user->getTwoFactorCode() && $user->getTwoFactorCodeExpiry() > new \DateTime()) {
                // Код верный, авторизуем пользователя
                $this->addFlash('success', '2FA verification successful!');
                return $this->redirectToRoute('app_profile');
            } else {
                // Код неверный или истек
                $this->addFlash('error', 'Invalid or expired code.');
            }
        }

        // Отображаем страницу ввода кода
        return $this->render('user/verify_2fa_code.html.twig');
    }
    #[Route('/profile/resend-2fa-code', name: 'app_resend_2fa', methods: ['POST'])]
    public function resendTwoFactorCode(Request $request, EntityManagerInterface $entityManager, TwoFactorAuthService $twoFactorAuthService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $code = random_int(100000, 999999);
        $user->setTwoFactorCode($code);
        $user->setTwoFactorCodeExpiry((new \DateTime())->modify('+5 minutes'));

        $method = $user->getTwoFactorMethod();
        if ($method === 'email') {
            $twoFactorAuthService->sendEmailCode($user->getEmail(), $code);
        } elseif ($method === 'telegram') {
            $twoFactorAuthService->sendTelegramCode($user->getTelegramId(), $code);
        }

        $entityManager->flush();

        $this->addFlash('success', 'New 2FA code sent!');
        return $this->redirectToRoute('app_2fa');
    }
    #[Route('/profile/update', name: 'app_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $user->setLastName($request->request->get('lastName'));
        $user->setFirstName($request->request->get('firstName'));
        $user->setMiddleName($request->request->get('middleName'));
        $user->setAge((int)$request->request->get('age'));
        $user->setUsername($request->request->get('username'));
        $user->setEmail($request->request->get('email'));

        $phoneValue = $request->request->get('phone');
        $user->setPhoneValue($phoneValue);

        $twoFactorMethod = $request->request->get('twoFactorMethod');
        $user->setTwoFactorMethod($twoFactorMethod);

        if ($twoFactorMethod === 'telegram') {
            $telegramId = $request->request->get('telegramId');
            $user->setTelegramId($telegramId);
        } else {
            $user->setTelegramId(null);
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirectToRoute('app_profile');
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Profile updated successfully.');

        return $this->redirectToRoute('app_profile');
    }
}