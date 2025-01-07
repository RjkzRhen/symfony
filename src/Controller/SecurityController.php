<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TwoFactorAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, TwoFactorAuthService $twoFactorAuthService, AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

            if ($user && password_verify($password, $user->getPassword())) {
                if ($user->isTwoFactorEnabled()) {
                    $code = random_int(100000, 999999);
                    $user->setTwoFactorCode($code);
                    $user->setTwoFactorCodeExpiry((new \DateTime())->modify('+5 minutes'));

                    $method = $user->getTwoFactorMethod();
                    if ($method === 'email') {
                        $twoFactorAuthService->sendEmailCode($user->getEmail(), $code);
                    } elseif ($method === 'telegram') {
                        $telegramId = $user->getTelegramId();
                        if (empty($telegramId)) {
                            throw new \RuntimeException('Telegram ID is not set.');
                        }
                        $twoFactorAuthService->sendTelegramCode($telegramId, $code);
                    }

                    $entityManager->flush();

                    $request->getSession()->set('2fa_user', $username);
                    return $this->redirectToRoute('app_2fa');
                } else {
                    $this->addFlash('success', 'Login successful!');
                    return $this->redirectToRoute('app_profile');
                }
            } else {
                $error = 'Invalid credentials.';
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'recaptcha_site_key' => $this->getParameter('recaptcha.site_key'),
        ]);
    }

    #[Route('/2fa', name: 'app_2fa', methods: ['GET', 'POST'])]
    public function twoFactor(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $username = $session->get('2fa_user');

        if (!$username) {
            throw new AccessDeniedHttpException('User is not in a two-factor authentication process.');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            throw new AccessDeniedHttpException('User not found.');
        }

        if ($request->isMethod('POST')) {
            $code = $request->request->get('code');

            if ($code === $user->getTwoFactorCode() && $user->getTwoFactorCodeExpiry() > new \DateTime()) {
                $session->remove('2fa_user');
                $this->addFlash('success', '2FA verification successful!');
                return $this->redirectToRoute('app_profile');
            } else {
                $this->addFlash('error', 'Invalid or expired code.');
            }
        }

        return $this->render('security/2fa.html.twig');
    }

    #[Route('/resend-2fa-code', name: 'app_resend_2fa', methods: ['POST'])]
    public function resendTwoFactorCode(Request $request, EntityManagerInterface $entityManager, TwoFactorAuthService $twoFactorAuthService): Response
    {
        $session = $request->getSession();
        $username = $session->get('2fa_user');

        if (!$username) {
            throw new AccessDeniedHttpException('User is not in a two-factor authentication process.');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            throw new AccessDeniedHttpException('User not found.');
        }

        $code = random_int(100000, 999999);
        $user->setTwoFactorCode($code);
        $user->setTwoFactorCodeExpiry((new \DateTime())->modify('+5 minutes'));

        $method = $user->getTwoFactorMethod();
        if ($method === 'email') {
            $twoFactorAuthService->sendEmailCode($user->getEmail(), $code);
        } elseif ($method === 'telegram') {
            $telegramId = $user->getTelegramId();
            if (empty($telegramId)) {
                throw new \RuntimeException('Telegram ID is not set.');
            }
            $twoFactorAuthService->sendTelegramCode($telegramId, $code);
        }

        $entityManager->flush();

        $this->addFlash('success', 'New 2FA code sent!');
        return $this->redirectToRoute('app_2fa');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}