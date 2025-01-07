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
    #[Route('/users', name: 'user_index', methods: ['GET'])]  // Определение маршрута для списка пользователей
    public function index(Request $request, EntityManagerInterface $entityManager): Response  // Метод для отображения списка пользователей
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');  // Проверяем, что пользователь имеет роль ADMIN

        $filterForm = $this->createForm(UserFilterType::class, null, [  // Создаем форму фильтрации
            'method' => 'GET',  // Указываем метод GET для формы
        ]);
        $filterForm->handleRequest($request);  // Обрабатываем запрос для формы

        $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('u');  // Создаем QueryBuilder для поиска пользователей

        $sortBy = $request->query->get('sortBy', 'lastName');  // Получаем параметр сортировки (по умолчанию 'lastName')
        $sortOrder = $request->query->get('sortOrder', 'ASC');  // Получаем порядок сортировки (по умолчанию 'ASC')

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {  // Если форма фильтрации отправлена и валидна
            $data = $filterForm->getData();  // Получаем данные из формы

            if ($data['filterField'] && $data['filterValue']) {  // Если указаны поле и значение для фильтрации
                $queryBuilder->andWhere('u.' . $data['filterField'] . ' LIKE :filterValue')  // Добавляем условие фильтрации
                ->setParameter('filterValue', '%' . $data['filterValue'] . '%');
            }

            if ($data['sortBy']) {  // Если указано поле для сортировки
                $queryBuilder->orderBy('u.' . $data['sortBy'], $data['sortOrder'] ?? 'ASC');  // Добавляем сортировку
            }
        } else {
            $queryBuilder->orderBy('u.' . $sortBy, $sortOrder);  // Сортируем по умолчанию
        }

        $users = $queryBuilder->getQuery()->getResult();  // Получаем результат запроса

        // Отображаем страницу списка пользователей с переданными данными
        return $this->render('user/index.html.twig', [
            'users' => $users,  // Передаем список пользователей
            'filterForm' => $filterForm->createView(),  // Передаем форму фильтрации
            'sortBy' => $sortBy,  // Передаем поле сортировки
            'sortOrder' => $sortOrder,  // Передаем порядок сортировки
        ]);
    }

    #[Route('/user/new', name: 'user_new', methods: ['GET', 'POST'])]  // Определение маршрута для создания нового пользователя
    public function new(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');  // Проверяем, что пользователь имеет роль ADMIN

        $user = new User();  // Создаем новый объект пользователя
        $form = $this->createForm(UserType::class, $user, ['is_admin' => true]);  // Создаем форму для нового пользователя

        $form->handleRequest($request);  // Обрабатываем запрос для формы
        if ($form->isSubmitted() && $form->isValid()) {  // Если форма отправлена и валидна
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());  // Хешируем пароль
            $user->setPassword($hashedPassword);  // Устанавливаем хешированный пароль
            $entityManager->persist($user);  // Подготавливаем объект для сохранения
            $entityManager->flush();  // Сохраняем изменения в базе данных

            return $this->redirectToRoute('user_index');  // Перенаправляем на страницу списка пользователей
        }

        // Отображаем страницу создания нового пользователя
        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),  // Передаем форму
        ]);
    }

    #[Route('/user/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]  // Определение маршрута для редактирования пользователя
    public function edit(
        Request $request,
        User $user,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $this->getUser();  // Получаем текущего пользователя

        if (!$this->isGranted('ROLE_ADMIN') && $currentUser->getId() !== $user->getId()) {  // Проверяем права на редактирование
            throw $this->createAccessDeniedException('You cannot edit this profile.');  // Выбрасываем исключение, если прав нет
        }

        $form = $this->createForm(UserType::class, $user, [  // Создаем форму для редактирования
            'is_admin' => $this->isGranted('ROLE_ADMIN'),  // Передаем флаг, является ли пользователь администратором
        ]);

        $form->handleRequest($request);  // Обрабатываем запрос для формы
        if ($form->isSubmitted() && $form->isValid()) {  // Если форма отправлена и валидна
            if ($user->getPassword()) {  // Если пароль был изменен
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());  // Хешируем новый пароль
                $user->setPassword($hashedPassword);  // Устанавливаем хешированный пароль
            }
            $entityManager->flush();  // Сохраняем изменения в базе данных

            return $this->redirectToRoute('user_index');  // Перенаправляем на страницу списка пользователей
        }

        // Отображаем страницу редактирования пользователя
        return $this->render('user/edit.html.twig', [
            'user' => $user,  // Передаем объект пользователя
            'form' => $form->createView(),  // Передаем форму
        ]);
    }

    #[Route('/user/{id}', name: 'user_show', methods: ['GET'])]  // Определение маршрута для просмотра пользователя
    public function show(User $user): Response  // Метод для отображения информации о пользователе
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,  // Передаем объект пользователя
        ]);
    }

    #[Route('/create-admin', name: 'create_admin')]  // Определение маршрута для создания администратора
    public function createAdmin(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response  // Метод для создания администратора
    {
        $user = new User();  // Создаем новый объект пользователя
        $user->setUsername('admin');  // Устанавливаем имя пользователя
        $user->setPassword($passwordHasher->hashPassword($user, 'admin123'));  // Хешируем пароль
        $user->setLastName('Admin');  // Устанавливаем фамилию
        $user->setFirstName('Admin');  // Устанавливаем имя
        $user->setMiddleName('Admin');  // Устанавливаем отчество
        $user->setAge(30);  // Устанавливаем возраст
        $user->setRoles(['ROLE_ADMIN']);  // Устанавливаем роль администратора

        $entityManager->persist($user);  // Подготавливаем объект для сохранения
        $entityManager->flush();  // Сохраняем изменения в базе данных

        return new Response('Администратор создан с логином "admin" и паролем "admin123".');  // Возвращаем сообщение об успешном создании
    }

    #[Route('/user/{id}/profile', name: 'user_profile', methods: ['GET'])]  // Определение маршрута для профиля пользователя
    public function profile(User $user): Response  // Метод для отображения профиля пользователя
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user,  // Передаем объект пользователя
        ]);
    }
}