<?php

namespace App\Controller; // Определяем пространство имен для контроллера

use App\Entity\User; // Подключаем сущность User
use App\Form\UserType; // Подключаем форму UserType
use Doctrine\ORM\EntityManagerInterface; // Подключаем интерфейс EntityManager
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Подключаем базовый класс контроллера
use Symfony\Component\HttpFoundation\Request; // Подключаем класс Request для обработки HTTP запросов
use Symfony\Component\HttpFoundation\Response; // Подключаем класс Response для возврата HTTP ответов
use Symfony\Component\Routing\Annotation\Route; // Подключаем аннотацию Route для определения маршрутов

class UserController extends AbstractController // Определяем класс контроллера, наследующийся от AbstractController
{
    // Маршрут для отображения списка всех пользователей
    #[Route('/users', name: 'app_users')] // Определяем маршрут для отображения списка пользователей
    public function index(EntityManagerInterface $entityManager): Response // Определяем метод index, который принимает EntityManager
    {
        // Получаем всех пользователей из базы данных
        $users = $entityManager->getRepository(User::class)->findAll(); // Получаем все записи из репозитория User

        // Возвращаем пользователей в шаблон
        return $this->render('user/index.html.twig', [ // Рендерим шаблон user/index.html.twig и передаем в него переменную users
            'users' => $users, // Передаем массив пользователей в шаблон
        ]);
    }

    // Маршрут для создания нового пользователя
    #[Route('/user/new', name: 'user_new')] // Определяем маршрут для создания нового пользователя
    public function new(Request $request, EntityManagerInterface $entityManager): Response // Определяем метод new, который принимает Request и EntityManager
    {
        $user = new User(); // Создаем новый объект User
        $form = $this->createForm(UserType::class, $user); // Создаем форму для объекта User

        $form->handleRequest($request); // Обрабатываем запрос и заполняем форму данными из запроса
        if ($form->isSubmitted() && $form->isValid()) { // Проверяем, была ли отправлена форма и является ли она валидной
            // Сохраняем нового пользователя в базе данных
            $entityManager->persist($user); // Говорим Doctrine сохранить объект User
            $entityManager->flush(); // Выполняем запрос к базе данных для сохранения объекта

            // Перенаправляем на страницу списка пользователей после успешного добавления
            return $this->redirectToRoute('app_users'); // Перенаправляем пользователя на маршрут app_users
        }

        return $this->render('user/new.html.twig', [ // Рендерим шаблон user/new.html.twig и передаем в него форму
            'form' => $form->createView(), // Передаем форму в шаблон
        ]);
    }
}