<?php

namespace App\Service;

use App\Entity\User;
use App\Service\TwoFactorAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class CustomAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface  // Определение класса аутентификатора
{
    private EntityManagerInterface $entityManager;  // EntityManager для работы с базой данных
    private RouterInterface $router;  // Router для работы с маршрутами
    private TwoFactorAuthService $twoFactorAuthService;  // Сервис для двухфакторной аутентификации
    private UserPasswordHasherInterface $passwordHasher;  // Сервис для хеширования паролей

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        TwoFactorAuthService $twoFactorAuthService,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->entityManager = $entityManager;  // Инициализация EntityManager
        $this->router = $router;  // Инициализация Router
        $this->twoFactorAuthService = $twoFactorAuthService;  // Инициализация сервиса двухфакторной аутентификации
        $this->passwordHasher = $passwordHasher;  // Инициализация сервиса хеширования паролей
    }

    public function supports(Request $request): ?bool  // Метод для проверки, поддерживается ли запрос
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');  // Проверяем, что запрос идет на маршрут входа и метод POST
    }

    public function authenticate(Request $request): Passport  // Метод для аутентификации
    {
        $username = $request->request->get('username');  // Получаем имя пользователя из запроса
        $password = $request->request->get('password');  // Получаем пароль из запроса

        if (empty($username) || empty($password)) {  // Проверяем, что имя пользователя и пароль не пустые
            throw new CustomUserMessageAuthenticationException('Username and password are required.');  // Выбрасываем исключение, если данные отсутствуют
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);  // Ищем пользователя в базе данных

        if (!$user) {  // Если пользователь не найден
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');  // Выбрасываем исключение
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {  // Проверяем, совпадает ли пароль
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');  // Выбрасываем исключение, если пароль неверный
        }

        if ($user->isTwoFactorEnabled()) {  // Если включена двухфакторная аутентификация
            $otp = $request->request->get('otp');  // Получаем OTP из запроса

            if (empty($otp)) {  // Если OTP отсутствует
                throw new CustomUserMessageAuthenticationException('OTP is required.');  // Выбрасываем исключение
            }

            if (!$this->twoFactorAuthService->verifyCode($user->getGoogleAuthenticatorSecret(), $otp)) {  // Проверяем OTP
                throw new CustomUserMessageAuthenticationException('Invalid OTP.');  // Выбрасываем исключение, если OTP неверный
            }
        }

        return new SelfValidatingPassport(new UserBadge($username));  // Возвращаем Passport для успешной аутентификации
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response  // Метод для обработки успешной аутентификации
    {
        return new RedirectResponse($this->router->generate('app_profile'));  // Перенаправляем на страницу профиля
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response  // Метод для обработки неудачной аутентификации
    {
        $request->getSession()->set('error', $exception->getMessage());  // Сохраняем сообщение об ошибке в сессии
        return new RedirectResponse($this->router->generate('app_login'));  // Перенаправляем на страницу входа
    }

    public function start(Request $request, AuthenticationException $authException = null): Response  // Метод для точки входа аутентификации
    {
        return new RedirectResponse($this->router->generate('app_login'));  // Перенаправляем на страницу входа
    }
}