<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Exception\ClientException;

class TwoFactorAuthService
{
    private MailerInterface $mailer;
    private HttpClientInterface $httpClient;
    private string $telegramBotToken;

    public function __construct(
        MailerInterface $mailer,
        HttpClientInterface $httpClient,
        string $telegramBotToken
    ) {
        $this->mailer = $mailer;
        $this->httpClient = $httpClient;
        $this->telegramBotToken = $telegramBotToken;
    }

    public function sendEmailCode(string $email, string $code): void
    {
        $emailMessage = (new Email())
            ->from('rjkz.rjk1@mail.ru')
            ->to($email)
            ->subject('Your 2FA Code')
            ->text("Your 2FA code is: $code");

        $this->mailer->send($emailMessage);
    }

    public function sendTelegramCode(string $telegramId, string $code): void
    {
        if (empty($telegramId)) {
            throw new \RuntimeException('Telegram ID is not set.');
        }

        $url = "https://api.telegram.org/bot{$this->telegramBotToken}/sendMessage";
        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => [
                    'chat_id' => $telegramId,
                    'text' => "Your 2FA code is: $code",
                ],
            ]);

            // Проверка статуса ответа
            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException('Failed to send Telegram message. Status code: ' . $response->getStatusCode());
            }
        } catch (ClientException $e) {
            // Логируем ошибку
            error_log('Failed to send Telegram message: ' . $e->getMessage());
            throw new \RuntimeException('Failed to send Telegram message: ' . $e->getMessage());
        }
    }
}