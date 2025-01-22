<?php

namespace App\Controller;

use App\Entity\Apartment;
use App\Form\ApartmentType;
use App\Repository\ApartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/apartment')]
class ApartmentController extends AbstractController
{
    // Определяет маршрут для отображения списка квартир
    #[Route('/', name: 'apartment_index', methods: ['GET'])]
    public function index(ApartmentRepository $apartmentRepository): Response
    {
        // Отображает список всех квартир
        return $this->render('apartment/index.html.twig', [
            'apartments' => $apartmentRepository->findAll(), // Список всех квартир
        ]);
    }

    // Определяет маршрут для создания новой квартиры
    #[Route('/new', name: 'apartment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Создает новый объект квартиры
        $apartment = new Apartment();
        // Создает форму для добавления квартиры
        $form = $this->createForm(ApartmentType::class, $apartment);
        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет квартиру
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($apartment); // Подготавливает объект для сохранения
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу списка квартир
            return $this->redirectToRoute('apartment_index', [], Response::HTTP_SEE_OTHER);
        }

        // Отображает страницу создания новой квартиры
        return $this->render('apartment/new.html.twig', [
            'apartment' => $apartment, // Объект квартиры
            'form' => $form, // Форма для создания квартиры
        ]);
    }

    // Определяет маршрут для просмотра информации о квартире
    #[Route('/{id}', name: 'apartment_show', methods: ['GET'])]
    public function show(Apartment $apartment): Response
    {
        // Отображает страницу с информацией о квартире
        return $this->render('apartment/show.html.twig', [
            'apartment' => $apartment, // Объект квартиры
        ]);
    }

    // Определяет маршрут для редактирования квартиры
    #[Route('/{id}/edit', name: 'apartment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Apartment $apartment, EntityManagerInterface $entityManager): Response
    {
        // Создает форму для редактирования квартиры
        $form = $this->createForm(ApartmentType::class, $apartment);
        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет изменения
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу списка квартир
            return $this->redirectToRoute('apartment_index', [], Response::HTTP_SEE_OTHER);
        }

        // Отображает страницу редактирования квартиры
        return $this->render('apartment/edit.html.twig', [
            'apartment' => $apartment, // Объект квартиры
            'form' => $form, // Форма для редактирования квартиры
        ]);
    }

    // Определяет маршрут для удаления квартиры
    #[Route('/{id}', name: 'apartment_delete', methods: ['POST'])]
    public function delete(Request $request, Apartment $apartment, EntityManagerInterface $entityManager): Response
    {
        // Проверяет CSRF-токен для безопасности
        if ($this->isCsrfTokenValid('delete'.$apartment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($apartment); // Удаляет квартиру
            $entityManager->flush(); // Сохраняет изменения в базе данных
        }

        // Перенаправляет на страницу списка квартир
        return $this->redirectToRoute('apartment_index', [], Response::HTTP_SEE_OTHER);
    }
}