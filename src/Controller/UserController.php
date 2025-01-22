<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserFilterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    // Определяет маршрут для отображения списка пользователей
    #[Route('/users', name: 'user_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Создает форму для фильтрации пользователей
        $filterForm = $this->createForm(UserFilterType::class, null, [
            'method' => 'GET', // Указывает метод GET для формы
        ]);
        $filterForm->handleRequest($request); // Обрабатывает запрос для формы

        // Создает QueryBuilder для поиска пользователей
        $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('u');

        // Получает параметры сортировки из запроса
        $sortBy = $request->query->get('sortBy', 'lastName'); // Поле сортировки по умолчанию
        $sortOrder = $request->query->get('sortOrder', 'ASC'); // Порядок сортировки по умолчанию

        // Применяет фильтрацию, если форма фильтрации отправлена и валидна
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData(); // Получает данные из формы

            if ($data['filterField'] && $data['filterValue']) {
                $queryBuilder->andWhere('u.' . $data['filterField'] . ' LIKE :filterValue') // Добавляет условие фильтрации
                ->setParameter('filterValue', '%' . $data['filterValue'] . '%');
            }

            if ($data['sortBy']) {
                $queryBuilder->orderBy('u.' . $data['sortBy'], $data['sortOrder'] ?? 'ASC'); // Добавляет сортировку
            }
        } else {
            $queryBuilder->orderBy('u.' . $sortBy, $sortOrder); // Сортирует по умолчанию
        }

        // Получает результат запроса
        $users = $queryBuilder->getQuery()->getResult();

        // Отображает страницу списка пользователей
        return $this->render('user/index.html.twig', [
            'users' => $users, // Список пользователей
            'filterForm' => $filterForm->createView(), // Форма фильтрации
            'sortBy' => $sortBy, // Поле сортировки
            'sortOrder' => $sortOrder, // Порядок сортировки
        ]);
    }

    // Определяет маршрут для создания нового пользователя
    #[Route('/user/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Создает новый объект пользователя
        $user = new User();
        // Создает форму для нового пользователя
        $form = $this->createForm(UserType::class, $user, ['is_admin' => true]);

        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет пользователя
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword()); // Хеширует пароль
            $user->setPassword($hashedPassword); // Устанавливает хешированный пароль
            $entityManager->persist($user); // Подготавливает объект для сохранения
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу списка пользователей
            return $this->redirectToRoute('user_index');
        }

        // Отображает страницу создания нового пользователя
        return $this->render('user/new.html.twig', [
            'form' => $form->createView(), // Форма для создания пользователя
        ]);
    }

    // Определяет маршрут для редактирования пользователя
    #[Route('/user/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Получает текущего пользователя
        $currentUser = $this->getUser();

        // Проверяет права на редактирование
        if (!$this->isGranted('ROLE_ADMIN') && $currentUser->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('You cannot edit this profile.'); // Выбрасывает исключение, если прав нет
        }

        // Создает форму для редактирования пользователя
        $form = $this->createForm(UserType::class, $user, [
            'is_admin' => $this->isGranted('ROLE_ADMIN'), // Передает флаг, является ли пользователь администратором
        ]);

        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет изменения
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword()); // Хеширует новый пароль
                $user->setPassword($hashedPassword); // Устанавливает хешированный пароль
            }
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу списка пользователей
            return $this->redirectToRoute('user_index');
        }

        // Отображает страницу редактирования пользователя
        return $this->render('user/edit.html.twig', [
            'user' => $user, // Объект пользователя
            'form' => $form->createView(), // Форма для редактирования пользователя
        ]);
    }

    // Определяет маршрут для просмотра информации о пользователе
    #[Route('/user/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // Отображает страницу с информацией о пользователе
        return $this->render('user/show.html.twig', [
            'user' => $user, // Объект пользователя
        ]);
    }

    // Определяет маршрут для создания администратора
    #[Route('/create-admin', name: 'create_admin')]
    public function createAdmin(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Создает новый объект пользователя
        $user = new User();
        $user->setUsername('admin'); // Устанавливает имя пользователя
        $user->setPassword($passwordHasher->hashPassword($user, 'admin123')); // Хеширует пароль
        $user->setLastName('Admin'); // Устанавливает фамилию
        $user->setFirstName('Admin'); // Устанавливает имя
        $user->setMiddleName('Admin'); // Устанавливает отчество
        $user->setAge(30); // Устанавливает возраст
        $user->setRoles(['ROLE_ADMIN']); // Устанавливает роль администратора

        // Сохраняет администратора в базе данных
        $entityManager->persist($user); // Подготавливает объект для сохранения
        $entityManager->flush(); // Сохраняет изменения в базе данных

        // Возвращает сообщение об успешном создании администратора
        return new Response('Администратор создан с логином "admin" и паролем "admin123".');
    }

    // Определяет маршрут для просмотра профиля пользователя
    #[Route('/user/{id}/profile', name: 'user_profile', methods: ['GET'])]
    public function profile(User $user): Response
    {
        // Отображает страницу профиля пользователя
        return $this->render('user/profile.html.twig', [
            'user' => $user, // Объект пользователя
        ]);
    }
}