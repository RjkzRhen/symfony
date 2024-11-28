<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserFilterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'user_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $filterForm = $this->createForm(UserFilterType::class, null, [
            'method' => 'GET',
        ]);

        $filterForm->handleRequest($request);

        $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('u');

        $sortBy = $request->query->get('sortBy', 'lastName'); // Значение по умолчанию
        $sortOrder = $request->query->get('sortOrder', 'ASC'); // Значение по умолчанию

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();

            if ($data['filterField'] && $data['filterValue']) {
                $queryBuilder->andWhere('u.' . $data['filterField'] . ' LIKE :filterValue')
                    ->setParameter('filterValue', '%' . $data['filterValue'] . '%');
            }

            if ($data['sortBy']) {
                $queryBuilder->orderBy('u.' . $data['sortBy'], $data['sortOrder'] ?? 'ASC');
            }
        } else {
            $queryBuilder->orderBy('u.' . $sortBy, $sortOrder);
        }

        $users = $queryBuilder->getQuery()->getResult();

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'filterForm' => $filterForm->createView(),
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    #[Route('/user/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}