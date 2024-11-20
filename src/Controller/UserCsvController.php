<?php

namespace App\Controller; // Определяем пространство имен для контроллера

use App\Entity\UserCsv; // Подключаем сущность UserCsv
use App\Form\UserCsvType; // Подключаем форму UserCsvType
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Подключаем базовый класс контроллера
use Symfony\Component\HttpFoundation\Request; // Подключаем класс Request для обработки HTTP запросов
use Symfony\Component\HttpFoundation\Response; // Подключаем класс Response для возврата HTTP ответов
use Symfony\Component\Routing\Annotation\Route; // Подключаем аннотацию Route для определения маршрутов

class UserCsvController extends AbstractController // Определяем класс контроллера, наследующийся от AbstractController
{
    // Маршрут для отображения CSV файла
    #[Route('/user-csv', name: 'user_csv_index')] // Определяем маршрут для отображения CSV файла
    public function index(): Response // Определяем метод index, который возвращает Response
    {
        $filePath = __DIR__ . '/../../exports/userCsv.csv'; // Определяем путь к CSV файлу

        // Проверяем, существует ли CSV файл
        if (!file_exists($filePath)) { // Проверяем, существует ли файл по указанному пути
            return new Response('CSV файл не найден', 404); // Возвращаем ответ с кодом 404, если файл не найден
        }

        // Открываем CSV файл
        $file = fopen($filePath, 'r'); // Открываем файл для чтения
        $users = []; // Инициализируем массив для хранения данных из CSV

        // Пропускаем заголовки (если они есть)
        $headers = fgetcsv($file); // Читаем первую строку (заголовки) и пропускаем её

        // Читаем все строки
        while (($data = fgetcsv($file)) !== false) { // Читаем строки из файла до тех пор, пока они не закончатся
            $users[] = $data; // Добавляем строку в массив users
        }

        fclose($file); // Закрываем файл

        // Отправляем данные в шаблон для отображения
        return $this->render('user_csv/index.html.twig', [ // Рендерим шаблон user_csv/index.html.twig и передаем в него массив users
            'users' => $users, // Передаем массив пользователей в шаблон
        ]);
    }

    // Маршрут для создания нового пользователя и сохранения его в CSV файл
    #[Route('/user-csv/new', name: 'user_csv_new')] // Определяем маршрут для создания нового пользователя и сохранения его в CSV файл
    public function new(Request $request): Response // Определяем метод new, который принимает Request
    {
        $userCsv = new UserCsv(); // Создаем новый объект UserCsv
        $form = $this->createForm(UserCsvType::class, $userCsv); // Создаем форму для объекта UserCsv

        $form->handleRequest($request); // Обрабатываем запрос и заполняем форму данными из запроса
        if ($form->isSubmitted() && $form->isValid()) { // Проверяем, была ли отправлена форма и является ли она валидной
            // Сохранение пользователя в CSV
            $this->writeToCsv($userCsv); // Вызываем метод для записи данных в CSV файл

            // Уведомление об успешном добавлении
            $this->addFlash('success', 'Пользователь успешно добавлен в CSV!'); // Добавляем флэш-сообщение об успешном добавлении

            // Перенаправление на текущую страницу после успешного добавления
            return $this->redirectToRoute('user_csv_new'); // Перенаправляем пользователя на маршрут user_csv_new
        }

        return $this->render('user_csv/new.html.twig', [ // Рендерим шаблон user_csv/new.html.twig и передаем в него форму
            'form' => $form->createView(), // Передаем форму в шаблон
        ]);
    }

    // Метод для записи данных пользователя в CSV файл
    private function writeToCsv(UserCsv $userCsv): void // Определяем приватный метод writeToCsv, который принимает объект UserCsv
    {
        $filePath = __DIR__ . '/../../exports/userCsv.csv'; // Определяем путь к CSV файлу

        // Если файл ещё не существует, добавляем заголовок
        $fileExists = file_exists($filePath); // Проверяем, существует ли файл по указанному пути
        $handle = fopen($filePath, 'a'); // Открываем файл для добавления данных в конец

        if (!$fileExists) { // Если файл не существует
            fputcsv($handle, ['Last Name', 'First Name', 'Middle Name', 'Age', 'Username', 'Password']); // Добавляем заголовок в файл
        }

        // Добавляем данные
        fputcsv($handle, [ // Добавляем строку с данными пользователя в файл
            $userCsv->lastName,
            $userCsv->firstName,
            $userCsv->middleName,
            $userCsv->age,
            $userCsv->username,
            $userCsv->password,
        ]);

        fclose($handle); // Закрываем файл
    }
}