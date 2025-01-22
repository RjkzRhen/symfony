<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\EmployeeDirectory;
use App\Form\EmployeeDirectoryFilterType;
use App\Form\EmployeeDirectorySortType;
use App\Form\EmployeeDirectoryType;

class EmployeeDirectoryController extends AbstractController
{
    // Определяет маршрут для отображения списка сотрудников
    #[Route('/employee/directory', name: 'employee_directory_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Создает форму для фильтрации
        $filterForm = $this->createForm(EmployeeDirectoryFilterType::class, null, [
            'method' => 'GET', // Указывает метод GET для формы
        ]);

        // Создает форму для сортировки
        $sortForm = $this->createForm(EmployeeDirectorySortType::class, null, [
            'method' => 'GET', // Указывает метод GET для формы
        ]);

        $filterForm->handleRequest($request); // Обрабатывает запрос для формы фильтрации
        $sortForm->handleRequest($request); // Обрабатывает запрос для формы сортировки

        // Создает QueryBuilder для поиска сотрудников
        $queryBuilder = $entityManager->getRepository(EmployeeDirectory::class)->createQueryBuilder('e');

        // Применяет фильтрацию, если форма фильтрации отправлена и валидна
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData(); // Получает данные из формы

            if ($data['filterField'] && $data['filterValue']) {
                $queryBuilder->andWhere('e.' . $data['filterField'] . ' LIKE :filterValue') // Добавляет условие фильтрации
                ->setParameter('filterValue', '%' . $data['filterValue'] . '%');
            }
        }

        // Применяет сортировку, если форма сортировки отправлена и валидна
        if ($sortForm->isSubmitted() && $sortForm->isValid()) {
            $data = $sortForm->getData(); // Получает данные из формы

            if ($data['sortBy']) {
                $queryBuilder->orderBy('e.' . $data['sortBy'], $data['sortOrder'] ?? 'ASC'); // Добавляет сортировку
            }
        }

        // Получает результат запроса
        $employees = $queryBuilder->getQuery()->getResult();

        // Отображает страницу списка сотрудников
        return $this->render('employee_directory/index.html.twig', [
            'employees' => $employees, // Список сотрудников
            'filterForm' => $filterForm->createView(), // Форма фильтрации
            'sortForm' => $sortForm->createView(), // Форма сортировки
        ]);
    }

    // Определяет маршрут для создания нового сотрудника
    #[Route('/employee/directory/new', name: 'employee_directory_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Создает новый объект сотрудника
        $employee = new EmployeeDirectory();
        // Создает форму для добавления сотрудника
        $form = $this->createForm(EmployeeDirectoryType::class, $employee);

        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет сотрудника
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employee); // Подготавливает объект для сохранения
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу списка сотрудников
            return $this->redirectToRoute('employee_directory_index');
        }

        // Отображает страницу создания нового сотрудника
        return $this->render('employee_directory/new.html.twig', [
            'form' => $form->createView(), // Форма для создания сотрудника
        ]);
    }

    // Определяет маршрут для просмотра информации о сотруднике
    #[Route('/employee/directory/{id}', name: 'employee_directory_show')]
    public function show(EmployeeDirectory $employee): Response
    {
        // Отображает страницу с информацией о сотруднике
        return $this->render('employee_directory/show.html.twig', [
            'employee' => $employee, // Объект сотрудника
        ]);
    }

    // Определяет маршрут для редактирования сотрудника
    #[Route('/employee/directory/{id}/edit', name: 'employee_directory_edit')]
    public function edit(Request $request, EmployeeDirectory $employee, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Создает форму для редактирования сотрудника
        $form = $this->createForm(EmployeeDirectoryType::class, $employee);

        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет изменения
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу списка сотрудников
            return $this->redirectToRoute('employee_directory_index');
        }

        // Отображает страницу редактирования сотрудника
        return $this->render('employee_directory/edit.html.twig', [
            'employee' => $employee, // Объект сотрудника
            'form' => $form->createView(), // Форма для редактирования сотрудника
        ]);
    }

    // Определяет маршрут для удаления сотрудника
    #[Route('/employee/directory/{id}/delete', name: 'employee_directory_delete', methods: ['POST'])]
    public function delete(Request $request, EmployeeDirectory $employee, EntityManagerInterface $entityManager): Response
    {
        // Проверяет, что пользователь имеет роль администратора
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Проверяет CSRF-токен для безопасности
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->request->get('_token'))) {
            $entityManager->remove($employee); // Удаляет сотрудника
            $entityManager->flush(); // Сохраняет изменения в базе данных
        }

        // Перенаправляет на страницу списка сотрудников
        return $this->redirectToRoute('employee_directory_index');
    }
}