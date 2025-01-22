<?php

namespace App\Controller;

use App\Entity\Counterparty;
use App\Form\CounterpartyType;
use App\Repository\CounterpartyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/counterparty')]
class CounterpartyController extends AbstractController
{
    #[Route('/', name: 'app_counterparty_index', methods: ['GET'])]
    public function index(CounterpartyRepository $counterpartyRepository): Response
    {
        // Возвращаем список всех контрагентов
        return $this->render('counterparty/index.html.twig', [
            'counterparties' => $counterpartyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_counterparty_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Создаем новый объект Counterparty
        $counterparty = new Counterparty();
        // Создаем форму для контрагента
        $form = $this->createForm(CounterpartyType::class, $counterparty);
        $form->handleRequest($request);

        // Если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            // Сохраняем контрагента в базе данных
            $entityManager->persist($counterparty);
            $entityManager->flush();

            // Перенаправляем на страницу списка контрагентов
            return $this->redirectToRoute('app_counterparty_index', [], Response::HTTP_SEE_OTHER);
        }

        // Рендерим страницу создания контрагента
        return $this->render('counterparty/new.html.twig', [
            'counterparty' => $counterparty,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_counterparty_show', methods: ['GET'])]
    public function show(Counterparty $counterparty): Response
    {
        // Рендерим страницу с деталями контрагента
        return $this->render('counterparty/show.html.twig', [
            'counterparty' => $counterparty,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_counterparty_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Counterparty $counterparty, EntityManagerInterface $entityManager): Response
    {
        // Создаем форму для редактирования контрагента
        $form = $this->createForm(CounterpartyType::class, $counterparty);
        $form->handleRequest($request);

        // Если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            // Сохраняем изменения в базе данных
            $entityManager->flush();

            // Перенаправляем на страницу списка контрагентов
            return $this->redirectToRoute('app_counterparty_index', [], Response::HTTP_SEE_OTHER);
        }

        // Рендерим страницу редактирования контрагента
        return $this->render('counterparty/edit.html.twig', [
            'counterparty' => $counterparty,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_counterparty_delete', methods: ['POST'])]
    public function delete(Request $request, Counterparty $counterparty, EntityManagerInterface $entityManager): Response
    {
        // Проверяем CSRF-токен
        if ($this->isCsrfTokenValid('delete'.$counterparty->getId(), $request->request->get('_token'))) {
            // Удаляем контрагента из базы данных
            $entityManager->remove($counterparty);
            $entityManager->flush();
        }

        // Перенаправляем на страницу списка контрагентов
        return $this->redirectToRoute('app_counterparty_index', [], Response::HTTP_SEE_OTHER);
    }
}