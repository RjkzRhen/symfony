<?php

namespace App\Controller;

use App\Service\TwoFactorAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test/email', name: 'app_test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('barannickoffnik@yandex.ru') // Новая почта отправителя
            ->to('qwora1@bk.ru') // Получатель
            ->subject('Тестовое сообщение')
            ->text('Это тестовое сообщение от Symfony.');

        $mailer->send($email);

        return new Response('Тестовое email сообщение отправлено!');
    }
    #[Route('/test-telegram', name: 'test_telegram')]
    public function testTelegram(TwoFactorAuthService $twoFactorAuthService): Response
    {
        // Отправка тестового сообщения в Telegram
        $twoFactorAuthService->sendTelegramCode('123456');

        return new Response('Test Telegram message sent!');
    }
}