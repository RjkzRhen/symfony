<?php

namespace App\Service;

use TelegramBot\Api\BotApi;

class TelegramService
{
    private $botToken;
    private $chatId;

    public function __construct(string $botToken, string $chatId)
    {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    public function sendMessage(string $message): void
    {
        $bot = new BotApi($this->botToken);
        $bot->sendMessage($this->chatId, $message);
    }
}