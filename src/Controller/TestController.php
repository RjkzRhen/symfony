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
    #[Route('/test/email', name: 'app_test_email')]  // Определение маршрута для тестирования email
    public function testEmail(MailerInterface $mailer): Response  // Метод для тестирования отправки email
    {
        $email = (new Email())  // Создаем email-сообщение
        ->from('barannickoffnik@yandex.ru')  // Указываем email отправителя
        ->to('qwora1@bk.ru')  // Указываем email получателя
        ->subject('Тестовое сообщение')  // Указываем тему письма
        ->text('Это тестовое сообщение от Symfony.');  // Указываем текст письма

        $mailer->send($email);  // Отправляем email

        return new Response('Тестовое email сообщение отправлено!');  // Возвращаем сообщение об успешной отправке
    }

    #[Route('/test-telegram', name: 'test_telegram')]  // Определение маршрута для тестирования Telegram
    public function testTelegram(TwoFactorAuthService $twoFactorAuthService): Response  // Метод для тестирования отправки сообщения в Telegram
    {
        // Отправка тестового сообщения в Telegram
        $twoFactorAuthService->sendTelegramCode('123456');  // Отправляем тестовый код

        return new Response('Test Telegram message sent!');  // Возвращаем сообщение об успешной отправке
    }
}