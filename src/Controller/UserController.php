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
    #[Route('/users', name: 'user_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $filterForm = $this->createForm(UserFilterType::class, null, [
            'method' => 'GET',
        ]);
        $filterForm->handleRequest($request);

        $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('u');

        $sortBy = $request->query->get('sortBy', 'lastName');
        $sortOrder = $request->query->get('sortOrder', 'ASC');

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
    public function new(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['is_admin' => true]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $this->getUser();

        if (!$this->isGranted('ROLE_ADMIN') && $currentUser->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('You cannot edit this profile.');
        }

        $form = $this->createForm(UserType::class, $user, [
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
            }
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
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

    #[Route('/create-admin', name: 'create_admin')]
    public function createAdmin(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($passwordHasher->hashPassword($user, 'admin123')); // Безопасный пароль
        $user->setRoles(['ROLE_ADMIN']); // Установка роли администратора

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Администратор создан с логином "admin" и паролем "admin123".');
    }
}