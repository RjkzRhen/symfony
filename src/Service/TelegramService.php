<?php

namespace App\Service;  // Определение пространства имен для сервиса

use TelegramBot\Api\BotApi;  // Импорт класса BotApi для работы с Telegram API

class TelegramService  // Определение класса сервиса для работы с Telegram
{
    private $botToken;  // Токен Telegram-бота
    private $chatId;  // ID чата в Telegram

    public function __construct(string $botToken, string $chatId)  // Конструктор для инициализации токена и ID чата
    {
        $this->botToken = $botToken;  // Инициализация токена
        $this->chatId = $chatId;  // Инициализация ID чата
    }

    public function sendMessage(string $message): void  // Метод для отправки сообщения в Telegram
    {
        $bot = new BotApi($this->botToken);  // Создаем объект BotApi
        $bot->sendMessage($this->chatId, $message);  // Отправляем сообщение
    }
}