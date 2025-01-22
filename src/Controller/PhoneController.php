<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Entity\User;
use App\Form\PhoneEditType;
use App\Form\PhoneFilterType;
use App\Form\PhoneType;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    // Определяет маршрут для отображения списка телефонов
    #[Route('/phones', name: 'phone_index', methods: ['GET'])]
    public function index(Request $request, PhoneRepository $phoneRepository, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Создает форму для фильтрации телефонов
        $filterForm = $this->createForm(PhoneFilterType::class, null, [
            'method' => 'GET', // Указывает метод GET для формы
        ]);

        $filterForm->handleRequest($request); // Обрабатывает запрос для формы

        // Создает QueryBuilder для поиска телефонов
        $queryBuilder = $entityManager->getRepository(Phone::class)->createQueryBuilder('p');

        // Применяет фильтрацию, если форма фильтрации отправлена и валидна
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData(); // Получает данные из формы

            if ($data['phone']) {
                $queryBuilder->andWhere('p.value LIKE :phone') // Добавляет условие фильтрации по номеру телефона
                ->setParameter('phone', '%' . $data['phone'] . '%');
            }

            if ($data['user']) {
                $queryBuilder->join('p.user', 'u') // Добавляет условие фильтрации по пользователю
                ->andWhere('u.lastName LIKE :user OR u.firstName LIKE :user OR u.middleName LIKE :user')
                    ->setParameter('user', '%' . $data['user'] . '%');
            }
        }

        // Получает результат запроса
        $phones = $queryBuilder->getQuery()->getResult();

        // Группирует телефоны по пользователям
        $groupedPhones = [];
        foreach ($phones as $phone) {
            $user = $phone->getUser(); // Получает пользователя для текущего телефона
            if (!isset($groupedPhones[$user->getId()])) {
                $groupedPhones[$user->getId()] = [
                    'user' => $user, // Пользователь
                    'phones' => [], // Список телефонов пользователя
                ];
            }
            $groupedPhones[$user->getId()]['phones'][] = $phone; // Добавляет телефон в список
        }

        // Отображает страницу списка телефонов
        return $this->render('phone/index.html.twig', [
            'groupedPhones' => $groupedPhones, // Группированные телефоны
            'filterForm' => $filterForm->createView(), // Форма фильтрации
        ]);
    }

    // Определяет маршрут для создания нового телефона
    #[Route('/phone/new', name: 'phone_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Создает новый объект телефона
        $phone = new Phone();
        // Создает форму для добавления телефона
        $form = $this->createForm(PhoneType::class, $phone);

        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет телефон
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->get('user')->getData(); // Получает пользователя из формы
            $phoneNumbers = $form->get('phones')->getData(); // Получает список номеров телефонов

            // Сохраняет каждый номер телефона
            foreach ($phoneNumbers as $phoneNumber) {
                $newPhone = new Phone(); // Создает новый объект телефона
                $newPhone->setUser($user); // Устанавливает пользователя для телефона
                $newPhone->setValue($phoneNumber); // Устанавливает номер телефона
                $entityManager->persist($newPhone); // Подготавливает объект для сохранения
            }

            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу списка телефонов
            return $this->redirectToRoute('phone_index');
        }

        // Отображает страницу создания нового телефона
        return $this->render('phone/new.html.twig', [
            'form' => $form->createView(), // Форма для создания телефона
        ]);
    }

    // Определяет маршрут для редактирования телефона
    #[Route('/phone/edit/{id}', name: 'phone_edit')]
    public function edit(Request $request, Phone $phone, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Создает форму для редактирования телефона
        $form = $this->createForm(PhoneEditType::class, $phone);

        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет изменения
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу списка телефонов
            return $this->redirectToRoute('phone_index');
        }

        // Отображает страницу редактирования телефона
        return $this->render('phone/edit.html.twig', [
            'form' => $form->createView(), // Форма для редактирования телефона
            'phone' => $phone, // Объект телефона
        ]);
    }

    // Определяет маршрут для удаления телефона
    #[Route('/phone/delete/{id}', name: 'phone_delete', methods: ['POST'])]
    public function delete(Request $request, Phone $phone, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Проверяет CSRF-токен для безопасности
        if ($this->isCsrfTokenValid('delete'.$phone->getId(), $request->request->get('_token'))) {
            $entityManager->remove($phone); // Удаляет телефон
            $entityManager->flush(); // Сохраняет изменения в базе данных
        }

        // Перенаправляет на страницу списка телефонов
        return $this->redirectToRoute('phone_index');
    }

    // Определяет маршрут для добавления телефона пользователю
    #[Route('/phone/add-to-user/{id}', name: 'phone_add_to_user')]
    public function addToUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Создает новый объект телефона
        $phone = new Phone();
        $phone->setUser($user); // Устанавливает пользователя для телефона

        // Создает форму для добавления телефона
        $form = $this->createForm(PhoneType::class, $phone);

        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет телефон
        if ($form->isSubmitted() && $form->isValid()) {
            $phoneNumbers = $form->get('phones')->getData(); // Получает список номеров телефонов

            // Сохраняет каждый номер телефона
            foreach ($phoneNumbers as $phoneNumber) {
                $newPhone = new Phone(); // Создает новый объект телефона
                $newPhone->setUser($user); // Устанавливает пользователя для телефона
                $newPhone->setValue($phoneNumber); // Устанавливает номер телефона
                $entityManager->persist($newPhone); // Подготавливает объект для сохранения
            }

            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу списка телефонов
            return $this->redirectToRoute('phone_index');
        }

        // Отображает страницу добавления телефона пользователю
        return $this->render('phone/add_to_user.html.twig', [
            'form' => $form->createView(), // Форма для добавления телефона
            'user' => $user, // Объект пользователя
        ]);
    }

    // Определяет маршрут для удаления пользователя
    #[Route('/user/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Проверяет CSRF-токен для безопасности
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user); // Удаляет пользователя
            $entityManager->flush(); // Сохраняет изменения в базе данных
        }

        // Перенаправляет на страницу списка пользователей
        return $this->redirectToRoute('user_index');
    }
}