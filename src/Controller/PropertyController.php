<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PropertyController extends AbstractController
{
    // Определяет маршрут для отображения списка недвижимости
    #[Route('/properties', name: 'property_index')]
    public function index(PropertyRepository $propertyRepository): Response
    {
        // Получает список всей недвижимости
        $properties = $propertyRepository->findAll();

        // Отображает страницу списка недвижимости
        return $this->render('property/index.html.twig', [
            'properties' => $properties, // Список недвижимости
        ]);
    }

    // Определяет маршрут для просмотра информации о конкретной недвижимости
    #[Route('/property/{id}', name: 'property_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, PropertyRepository $propertyRepository): Response
    {
        // Находит объект Property по ID
        $property = $propertyRepository->find($id);

        // Если объект не найден, выбрасывает исключение
        if (!$property) {
            throw $this->createNotFoundException('Квартира с ID ' . $id . ' не найдена.');
        }

        // Отображает страницу с информацией о недвижимости
        return $this->render('property/show.html.twig', [
            'property' => $property, // Объект недвижимости
        ]);
    }

    // Определяет маршрут для страницы "О нас"
    #[Route('/about-us', name: 'about_us')]
    public function aboutUs(): Response
    {
        // Отображает страницу "О нас"
        return $this->render('property/about_us.html.twig');
    }

    // Определяет маршрут для добавления тестовых данных о недвижимости
    #[Route('/add-sample-properties', name: 'add_sample_properties')]
    public function addSampleProperties(EntityManagerInterface $entityManager): Response
    {
        // Создает первую тестовую квартиру
        $property1 = new Property();
        $property1->setTitle('Студия'); // Устанавливает название
        $property1->setDescription('Уютная студия в центре города.'); // Устанавливает описание
        $property1->setPrice('1500000'); // Устанавливает цену
        $property1->setRooms('1'); // Устанавливает количество комнат
        $property1->setArea('35.5'); // Устанавливает площадь
        $property1->setImage('studio.jpg'); // Устанавливает изображение

        // Создает вторую тестовую квартиру
        $property2 = new Property();
        $property2->setTitle('1-комнатная квартира'); // Устанавливает название
        $property2->setDescription('Светлая квартира с видом на парк.'); // Устанавливает описание
        $property2->setPrice('2500000'); // Устанавливает цену
        $property2->setRooms('1'); // Устанавливает количество комнат
        $property2->setArea('45.0'); // Устанавливает площадь
        $property2->setImage('one-bedroom.jpg'); // Устанавливает изображение

        // Сохраняет тестовые данные в базе данных
        $entityManager->persist($property1); // Подготавливает первую квартиру для сохранения
        $entityManager->persist($property2); // Подготавливает вторую квартиру для сохранения
        $entityManager->flush(); // Сохраняет изменения в базе данных

        // Возвращает сообщение об успешном добавлении тестовых данных
        return new Response('Тестовые данные добавлены!');
    }

    // Определяет маршрут для создания новой недвижимости
    #[Route('/property/new', name: 'property_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Проверяет, что пользователь имеет роль администратора
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Создает новый объект недвижимости
        $property = new Property();
        // Создает форму для добавления недвижимости
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет недвижимость
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($property); // Подготавливает объект для сохранения
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Добавляет сообщение об успешном добавлении недвижимости
            $this->addFlash('success', 'Квартира успешно добавлена!');
            return $this->redirectToRoute('property_index'); // Перенаправляет на страницу списка недвижимости
        }

        // Отображает страницу создания новой недвижимости
        return $this->render('property/new.html.twig', [
            'form' => $form->createView(), // Форма для создания недвижимости
        ]);
    }

    // Определяет маршрут для редактирования недвижимости
    #[Route('/property/{id}/edit', name: 'property_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Проверяет, что пользователь имеет роль администратора
    public function edit(Request $request, int $id, PropertyRepository $propertyRepository, EntityManagerInterface $entityManager): Response
    {
        // Находит объект Property по ID
        $property = $propertyRepository->find($id);

        // Если объект не найден, выбрасывает исключение
        if (!$property) {
            throw $this->createNotFoundException('Квартира с ID ' . $id . ' не найдена.');
        }

        // Создает форму для редактирования недвижимости
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет изменения
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Добавляет сообщение об успешном обновлении недвижимости
            $this->addFlash('success', 'Квартира успешно обновлена!');
            return $this->redirectToRoute('property_index'); // Перенаправляет на страницу списка недвижимости
        }

        // Отображает страницу редактирования недвижимости
        return $this->render('property/edit.html.twig', [
            'property' => $property, // Объект недвижимости
            'form' => $form->createView(), // Форма для редактирования недвижимости
        ]);
    }

    // Определяет маршрут для удаления недвижимости
    #[Route('/property/{id}/delete', name: 'property_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')] // Проверяет, что пользователь имеет роль администратора
    public function delete(Request $request, int $id, PropertyRepository $propertyRepository, EntityManagerInterface $entityManager): Response
    {
        // Находит объект Property по ID
        $property = $propertyRepository->find($id);

        // Если объект не найден, выбрасывает исключение
        if (!$property) {
            throw $this->createNotFoundException('Квартира с ID ' . $id . ' не найдена.');
        }

        // Проверяет CSRF-токен для безопасности
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->request->get('_token'))) {
            $entityManager->remove($property); // Удаляет недвижимость
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Добавляет сообщение об успешном удалении недвижимости
            $this->addFlash('success', 'Квартира успешно удалена!');
        }

        // Перенаправляет на страницу списка недвижимости
        return $this->redirectToRoute('property_index');
    }
}