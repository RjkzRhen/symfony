<?php

namespace App\Controller;

use App\Entity\Unit;
use App\Form\UnitType;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/unit')]
class UnitController extends AbstractController
{
    #[Route('/', name: 'unit_index', methods: ['GET'])]
    public function index(UnitRepository $unitRepository): Response
    {
        // Возвращаем список всех единиц измерения
        return $this->render('unit/index.html.twig', [
            'units' => $unitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'unit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Создаем новый объект Unit
        $unit = new Unit();
        // Создаем форму для единицы измерения
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        // Если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            // Сохраняем единицу измерения в базе данных
            $entityManager->persist($unit);
            $entityManager->flush();

            // Перенаправляем на страницу списка единиц измерения
            return $this->redirectToRoute('unit_index', [], Response::HTTP_SEE_OTHER);
        }

        // Рендерим страницу создания единицы измерения
        return $this->render('unit/new.html.twig', [
            'unit' => $unit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'unit_show', methods: ['GET'])]
    public function show(Unit $unit): Response
    {
        // Рендерим страницу с деталями единицы измерения
        return $this->render('unit/show.html.twig', [
            'unit' => $unit,
        ]);
    }

    #[Route('/{id}/edit', name: 'unit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Unit $unit, EntityManagerInterface $entityManager): Response
    {
        // Создаем форму для редактирования единицы измерения
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        // Если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            // Сохраняем изменения в базе данных
            $entityManager->flush();

            // Перенаправляем на страницу списка единиц измерения
            return $this->redirectToRoute('unit_index', [], Response::HTTP_SEE_OTHER);
        }

        // Рендерим страницу редактирования единицы измерения
        return $this->render('unit/edit.html.twig', [
            'unit' => $unit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'unit_delete', methods: ['POST'])]
    public function delete(Request $request, Unit $unit, EntityManagerInterface $entityManager): Response
    {
        // Проверяем CSRF-токен
        if ($this->isCsrfTokenValid('delete'.$unit->getId(), $request->request->get('_token'))) {
            // Удаляем единицу измерения из базы данных
            $entityManager->remove($unit);
            $entityManager->flush();
        }

        // Перенаправляем на страницу списка единиц измерения
        return $this->redirectToRoute('unit_index', [], Response::HTTP_SEE_OTHER);
    }
}