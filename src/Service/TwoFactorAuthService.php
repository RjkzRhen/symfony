<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TwoFactorAuthService
{
private MailerInterface $mailer;

public function __construct(MailerInterface $mailer)
{
$this->mailer = $mailer;
}

/**
* Генерирует и отправляет код 2FA на указанный email.
*
* @param string $email Email, на который будет отправлен код.
* @return string Сгенерированный код.
*/
public function generateAndSendCode(string $email): string
{
// Генерация 4-значного кода
$code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

// Создание email
$emailMessage = (new Email())
->from('testSYMFONY25@yandex.com') // Укажите ваш email
->to($email)
->subject('Ваш код для 2FA')
->text('Ваш код для двухфакторной аутентификации: ' . $code);

// Отправка email
$this->mailer->send($emailMessage);

// Возвращаем код для сохранения в базе данных
return $code;
}
}