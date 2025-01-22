<?php

// src/Controller/TwoFactorAuthController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TwoFactorAuthService;

class TwoFactorAuthController extends AbstractController
{
    private TwoFactorAuthService $twoFactorAuthService;

    public function __construct(TwoFactorAuthService $twoFactorAuthService)
    {
        $this->twoFactorAuthService = $twoFactorAuthService;
    }

    #[Route('/enable-2fa', name: 'app_enable_2fa', methods: ['GET', 'POST'])]
    public function enable2FA(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('Ожидаемый пользователь не найден или имеет неверный тип.');
        }

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            if (empty($email)) {
                $this->addFlash('error', 'Пожалуйста, укажите email для получения кода 2FA.');
                return $this->redirectToRoute('app_enable_2fa');
            }

            // Генерация и отправка кода
            $code = $this->twoFactorAuthService->generateAndSendCode($email);

            // Сохранение кода и настройка 2FA
            $user->setTwoFactorCode($code);
            $user->setTwoFactorCodeExpiry(new \DateTime('+5 minutes'));
            $user->setIsTwoFactorEnabled(true);
            $user->setEmail($email); // Сохраняем email для 2FA
            $entityManager->flush();

            $this->addFlash('success', 'Код для 2FA отправлен на ваш email.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('security/enable_2fa.html.twig');
    }

    #[Route('/disable-2fa', name: 'app_disable_2fa')]
    public function disable2FA(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user) {
            $user->setIsTwoFactorEnabled(false);
            $user->setTwoFactorCode(null);
            $user->setTwoFactorCodeExpiry(null);
            $entityManager->flush();

            $this->addFlash('success', '2FA отключена.');
        }

        return $this->redirectToRoute('app_profile');
    }
}