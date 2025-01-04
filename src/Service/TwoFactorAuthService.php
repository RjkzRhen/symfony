<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwoFactorAuthService
{
    private MailerInterface $mailer;
    private HttpClientInterface $httpClient;
    private string $telegramBotToken;
    private string $telegramChatId;

    public function __construct(
        MailerInterface $mailer,
        HttpClientInterface $httpClient,
        string $telegramBotToken,
        string $telegramChatId
    ) {
        $this->mailer = $mailer;
        $this->httpClient = $httpClient;
        $this->telegramBotToken = $telegramBotToken;
        $this->telegramChatId = $telegramChatId;
    }

    public function sendEmailCode(string $email, string $code): void
    {
        $emailMessage = (new Email())
            ->from('rjkz.rjk1@mail.ru') // Укажите email отправителя
            ->to($email)
            ->subject('Your 2FA Code')
            ->text("Your 2FA code is: $code");

        $this->mailer->send($emailMessage);
    }

    public function sendTelegramCode(string $code): void
    {
        $url = "https://api.telegram.org/bot{$this->telegramBotToken}/sendMessage";
        $this->httpClient->request('POST', $url, [
            'json' => [
                'chat_id' => $this->telegramChatId,
                'text' => "Your 2FA code is: $code",
            ],
        ]);
    }
}
