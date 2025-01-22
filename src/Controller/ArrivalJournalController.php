<?php

namespace App\Controller;

use App\Entity\ArrivalJournal;
use App\Entity\ArrivalJournalDetail;
use App\Form\ArrivalJournalDetailType;
use App\Form\ArrivalJournalType;
use App\Repository\ArrivalJournalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;

#[Route('/admin/arrival-journal')]
class ArrivalJournalController extends AbstractController
{
    private $security;

    // Внедряем сервис Security через конструктор
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'arrival_journal_index', methods: ['GET'])]
    public function index(ArrivalJournalRepository $arrivalJournalRepository): Response
    {
        // Возвращаем список всех журналов прихода
        return $this->render('arrival_journal/index.html.twig', [
            'arrival_journals' => $arrivalJournalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'arrival_journal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Создаем новый объект ArrivalJournal
        $arrivalJournal = new ArrivalJournal();
        // Создаем форму для журнала прихода
        $form = $this->createForm(ArrivalJournalType::class, $arrivalJournal);
        $form->handleRequest($request);

        // Если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            // Получаем текущего пользователя
            $user = $this->security->getUser();

            // Проверяем, что пользователь аутентифицирован и является экземпляром User
            if (!$user instanceof User) {
                throw new \LogicException('Ожидаемый пользователь не найден или имеет неверный тип.');
            }

            // Устанавливаем пользователя, который создал запись
            $arrivalJournal->setCreatedBy($user);

            // Сохраняем журнал прихода в базе данных
            $entityManager->persist($arrivalJournal);
            $entityManager->flush();

            // Перенаправляем на страницу списка журналов прихода
            return $this->redirectToRoute('arrival_journal_index', [], Response::HTTP_SEE_OTHER);
        }

        // Рендерим страницу создания журнала прихода
        return $this->render('arrival_journal/new.html.twig', [
            'arrival_journal' => $arrivalJournal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'arrival_journal_show', methods: ['GET'])]
    public function show(ArrivalJournal $arrivalJournal): Response
    {
        // Рендерим страницу с деталями журнала прихода
        return $this->render('arrival_journal/show.html.twig', [
            'arrival_journal' => $arrivalJournal,
        ]);
    }

    #[Route('/{id}/edit', name: 'arrival_journal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ArrivalJournal $arrivalJournal, EntityManagerInterface $entityManager): Response
    {
        // Создаем форму для редактирования журнала прихода
        $form = $this->createForm(ArrivalJournalType::class, $arrivalJournal);
        $form->handleRequest($request);

        // Если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            // Получаем текущего пользователя
            $user = $this->security->getUser();

            // Проверяем, что пользователь аутентифицирован и является экземпляром User
            if (!$user instanceof User) {
                throw new \LogicException('Ожидаемый пользователь не найден или имеет неверный тип.');
            }

            // Устанавливаем пользователя, который обновил запись
            $arrivalJournal->setCreatedBy($user);

            // Явно обновляем поле updatedAt
            $arrivalJournal->setUpdatedAt(new \DateTimeImmutable());

            // Сохраняем изменения в базе данных
            $entityManager->flush();

            // Перенаправляем на страницу списка журналов прихода
            return $this->redirectToRoute('arrival_journal_index', [], Response::HTTP_SEE_OTHER);
        }

        // Рендерим страницу редактирования журнала прихода
        return $this->render('arrival_journal/edit.html.twig', [
            'arrival_journal' => $arrivalJournal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'arrival_journal_delete', methods: ['POST'])]
    public function delete(Request $request, ArrivalJournal $arrivalJournal, EntityManagerInterface $entityManager): Response
    {
        // Проверяем CSRF-токен
        if ($this->isCsrfTokenValid('delete'.$arrivalJournal->getId(), $request->request->get('_token'))) {
            // Получаем текущего пользователя
            $user = $this->security->getUser();

            // Проверяем, что пользователь аутентифицирован и является экземпляром User
            if (!$user instanceof User) {
                throw new \LogicException('Ожидаемый пользователь не найден или имеет неверный тип.');
            }

            // Устанавливаем пользователя, который удалил запись
            $arrivalJournal->setDeletedBy($user);
            $arrivalJournal->setDeletedAt(new \DateTimeImmutable());

            // Сохраняем изменения в базе данных
            $entityManager->flush();
        }

        // Перенаправляем на страницу списка журналов прихода
        return $this->redirectToRoute('arrival_journal_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/detail', name: 'arrival_journal_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, ArrivalJournal $arrivalJournal, EntityManagerInterface $entityManager): Response
    {
        // Создаем новый объект ArrivalJournalDetail
        $detail = new ArrivalJournalDetail();
        // Создаем форму для детали журнала прихода
        $form = $this->createForm(ArrivalJournalDetailType::class, $detail);
        $form->handleRequest($request);

        // Если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            // Устанавливаем связь с журналом прихода
            $detail->setArrivalJournal($arrivalJournal);
            $entityManager->persist($detail);

            // Обновляем время изменения журнала
            $arrivalJournal->setUpdatedAtValue();
            $entityManager->flush();

            // Перенаправляем на страницу с деталями журнала прихода
            return $this->redirectToRoute('arrival_journal_show', ['id' => $arrivalJournal->getId()]);
        }

        // Рендерим страницу добавления детали журнала прихода
        return $this->render('arrival_journal/detail.html.twig', [
            'arrival_journal' => $arrivalJournal,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/arrival-journal-detail/{id}/delete', name: 'arrival_journal_detail_delete', methods: ['DELETE'])]
    public function deleteDetail(Request $request, ArrivalJournalDetail $detail, EntityManagerInterface $entityManager): Response
    {
        // Проверяем CSRF-токен
        if ($this->isCsrfTokenValid('delete' . $detail->getId(), $request->request->get('_token'))) {
            // Удаляем деталь журнала прихода
            $entityManager->remove($detail);
            $entityManager->flush();
        }

        // Перенаправляем на страницу журнала прихода
        return $this->redirectToRoute('arrival_journal_show', ['id' => $detail->getArrivalJournal()->getId()]);
    }

    #[Route('/admin/arrival-journal-detail/{id}/edit', name: 'arrival_journal_detail_edit', methods: ['GET', 'POST'])]
    public function editDetail(Request $request, ArrivalJournalDetail $detail, EntityManagerInterface $entityManager): Response
    {
        // Создаем форму для редактирования детали журнала прихода
        $form = $this->createForm(ArrivalJournalDetailType::class, $detail);
        $form->handleRequest($request);

        // Если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            // Получаем родительскую сущность ArrivalJournal
            $arrivalJournal = $detail->getArrivalJournal();

            // Обновляем время обновления родительской сущности
            $arrivalJournal->setUpdatedAt(new \DateTimeImmutable());

            // Сохраняем изменения в базе данных
            $entityManager->flush();

            // Перенаправляем на страницу с деталями журнала прихода
            return $this->redirectToRoute('arrival_journal_show', ['id' => $arrivalJournal->getId()]);
        }

        // Рендерим страницу редактирования детали журнала прихода
        return $this->render('arrival_journal/detail_edit.html.twig', [
            'detail' => $detail,
            'form' => $form->createView(),
        ]);
    }
}