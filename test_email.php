<?php

require 'vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$dsn = 'smtp://rjkz.rjk1@mail.ru:0kP1jMZmjDXWTffp34e1@smtp.mail.ru:465';
$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);

$email = (new Email())
    ->from('rjkz.rjk1@mail.ru')
    ->to('qwora1@bk.ru')
    ->subject('Тестовое сообщение')
    ->text('Это тестовое сообщение от Symfony.');

$mailer->send($email);

echo 'Тестовое email сообщение отправлено!';