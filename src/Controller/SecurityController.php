<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SecurityController extends AbstractController
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    // Внедряем зависимости через конструктор
    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        // Получаем ошибку аутентификации, если она есть
        $error = $authenticationUtils->getLastAuthenticationError();
        // Получаем последнее имя пользователя
        $lastUsername = $authenticationUtils->getLastUsername();

        // Если пользователь уже аутентифицирован, перенаправляем на страницу профиля
        if ($this->getUser()) {
            $this->logger->info('User is already authenticated, redirecting to profile.');
            return $this->redirectToRoute('app_profile');
        }

        // Обрабатываем POST-запрос
        if ($request->isMethod('POST')) {
            // Получаем имя пользователя и пароль из запроса
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            // Получаем ответ reCAPTCHA
            $recaptchaResponse = $request->request->get('g-recaptcha-response');

            // Проверяем, заполнена ли капча
            if (empty($recaptchaResponse)) {
                $error = 'Пожалуйста, пройдите капчу.';
                $this->logger->warning('CAPTCHA not completed.');
                return $this->render('security/login.html.twig', [
                    'last_username' => $lastUsername,
                    'error' => $error,
                    'recaptcha_site_key' => $this->getParameter('recaptcha.site_key'),
                ]);
            }

            // Проверяем капчу через Google API
            $recaptchaSecret = $this->getParameter('recaptcha.secret_key');
            $recaptchaVerify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
            $recaptchaData = json_decode($recaptchaVerify);

            // Если капча не пройдена
            if (!$recaptchaData->success) {
                $error = 'Неверная капча. Пожалуйста, попробуйте снова.';
                $this->logger->warning('Invalid CAPTCHA.');
                return $this->render('security/login.html.twig', [
                    'last_username' => $lastUsername,
                    'error' => $error,
                    'recaptcha_site_key' => $this->getParameter('recaptcha.site_key'),
                ]);
            }

            // Ищем пользователя в базе данных
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

            // Проверяем пароль
            if ($user && password_verify($password, $user->getPassword())) {
                $this->logger->info('User authenticated successfully: ' . $user->getUsername());

                // Если включена двухфакторная аутентификация
                if ($user->isTwoFactorEnabled()) {
                    $this->logger->info('2FA is enabled for user: ' . $user->getUsername());
                    // Перенаправляем на страницу отправки кода 2FA
                    return $this->redirectToRoute('app_send_2fa_code');
                } else {
                    $this->logger->info('2FA is disabled for user: ' . $user->getUsername());
                    $this->addFlash('success', 'Вход выполнен успешно!');
                    return $this->redirectToRoute('app_profile');
                }
            } else {
                $error = 'Неверные учетные данные.';
                $this->logger->warning('Invalid credentials for user: ' . $username);
            }
        }

        // Рендерим страницу входа
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'recaptcha_site_key' => $this->getParameter('recaptcha.site_key'),
        ]);
    }

    #[Route('/send-2fa-code', name: 'app_send_2fa_code')]
    public function send2FACode(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Получаем текущего пользователя
        $user = $this->getUser();

        // Проверяем, что пользователь аутентифицирован и является экземпляром User
        if (!$user instanceof User) {
            throw new \LogicException('Ожидаемый пользователь не найден или имеет неверный тип.');
        }

        // Обрабатываем POST-запрос
        if ($request->isMethod('POST')) {
            // Генерация кода 2FA
            $code = rand(1000, 9999);
            $user->setTwoFactorCode($code);
            $user->setTwoFactorCodeExpiry(new \DateTime('+5 minutes'));
            $entityManager->flush();

            // Отправка кода на email
            $email = (new Email())
                ->from('testSYMFONY25@yandex.com')
                ->to($user->getEmail())
                ->subject('Ваш код для 2FA')
                ->text('Ваш код для двухфакторной аутентификации: ' . $code);

            try {
                $this->mailer->send($email);
                $this->addFlash('success', 'Код для 2FA отправлен на ваш email.');
                return $this->redirectToRoute('app_verify_2fa');  // Перенаправляем на страницу ввода кода
            } catch (\Exception $e) {
                $this->addFlash('error', 'Не удалось отправить код на email. Пожалуйста, попробуйте снова.');
                return $this->redirectToRoute('app_send_2fa_code');
            }
        }

        // Рендерим страницу отправки кода 2FA
        return $this->render('security/send_2fa_code.html.twig');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Метод выхода из системы, перехватывается firewall
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}