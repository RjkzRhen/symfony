<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Verify2FAController extends AbstractController
{
    #[Route('/verify-2fa', name: 'app_verify_2fa')]
    public function verify2FA(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        // Получаем текущего пользователя
        $user = $this->getUser();

        // Проверяем, что пользователь аутентифицирован и является экземпляром User
        if (!$user instanceof User) {
            // Логируем предупреждение, если пользователь не аутентифицирован или не является экземпляром User
            $logger->warning('User is not authenticated or is not an instance of User.');
            // Выбрасываем исключение с сообщением об ошибке
            throw new \LogicException('Ожидаемый пользователь не найден или имеет неверный тип.');
        }

        // Обрабатываем POST-запрос
        if ($request->isMethod('POST')) {
            // Получаем код 2FA из запроса
            $code = $request->request->get('code');

            // Проверяем, совпадает ли код и не истек ли срок его действия
            if ($user->getTwoFactorCode() === $code && $user->getTwoFactorCodeExpiry() > new \DateTime()) {
                // Очищаем код 2FA и срок его действия
                $user->setTwoFactorCode(null);
                $user->setTwoFactorCodeExpiry(null);
                $entityManager->flush(); // Сохраняем изменения в базе данных

                // Добавляем сообщение об успешной проверке 2FA
                $this->addFlash('success', '2FA подтверждена.');
                // Перенаправляем пользователя на страницу профиля
                return $this->redirectToRoute('app_profile');
            } else {
                // Добавляем сообщение об ошибке, если код неверен или истек срок действия
                $this->addFlash('error', 'Неверный код или срок действия истек.');
                // Логируем предупреждение о неверном коде или истекшем сроке действия
                $logger->warning('Invalid 2FA code or expired code for user: ' . $user->getUsername());
            }
        }

        // Рендерим страницу для ввода кода 2FA
        return $this->render('security/verify_2fa.html.twig');
    }
}