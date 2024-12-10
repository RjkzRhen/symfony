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
    #[Route('/phones', name: 'phone_index', methods: ['GET'])]
    public function index(Request $request, PhoneRepository $phoneRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $filterForm = $this->createForm(PhoneFilterType::class, null, [
            'method' => 'GET',
        ]);

        $filterForm->handleRequest($request);

        $queryBuilder = $entityManager->getRepository(Phone::class)->createQueryBuilder('p');

        $sortBy = $request->query->get('sortBy', 'user.lastName'); // Значение по умолчанию
        $sortOrder = $request->query->get('sortOrder', 'ASC'); // Значение по умолчанию

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();

            if ($data['filterField'] && $data['filterValue']) {
                if ($data['filterField'] === 'user') {
                    $queryBuilder->join('p.user', 'u')
                        ->andWhere('u.lastName LIKE :filterValue OR u.firstName LIKE :filterValue OR u.middleName LIKE :filterValue')
                        ->setParameter('filterValue', '%' . $data['filterValue'] . '%');
                } else {
                    $queryBuilder->andWhere('p.' . $data['filterField'] . ' LIKE :filterValue')
                        ->setParameter('filterValue', '%' . $data['filterValue'] . '%');
                }
            }

            if ($data['sortBy']) {
                if ($data['sortBy'] === 'user') {
                    $queryBuilder->join('p.user', 'u')
                        ->orderBy('u.lastName', $data['sortOrder'] ?? 'ASC');
                } else {
                    $queryBuilder->orderBy('p.' . $data['sortBy'], $data['sortOrder'] ?? 'ASC');
                }
            }
        } else {
            if (strpos($sortBy, 'user.') === 0) {
                $userField = substr($sortBy, 5); // Убираем 'user.' из строки
                $queryBuilder->join('p.user', 'u')
                    ->orderBy('u.' . $userField, $sortOrder);
            } else {
                $queryBuilder->orderBy('p.' . $sortBy, $sortOrder);
            }
        }

        $phones = $queryBuilder->getQuery()->getResult();

        $groupedPhones = [];
        foreach ($phones as $phone) {
            $user = $phone->getUser();
            if (!isset($groupedPhones[$user->getId()])) {
                $groupedPhones[$user->getId()] = [
                    'user' => $user,
                    'phones' => [],
                ];
            }
            $groupedPhones[$user->getId()]['phones'][] = $phone;
        }

        return $this->render('phone/index.html.twig', [
            'groupedPhones' => $groupedPhones,
            'filterForm' => $filterForm->createView(),
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    #[Route('/phone/new', name: 'phone_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $phone = new Phone();
        $form = $this->createForm(PhoneType::class, $phone);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->get('user')->getData();
            $phoneNumbers = $form->get('phones')->getData();

            foreach ($phoneNumbers as $phoneNumber) {
                $newPhone = new Phone();
                $newPhone->setUser($user);
                $newPhone->setValue($phoneNumber);
                $entityManager->persist($newPhone);
            }

            $entityManager->flush();

            return $this->redirectToRoute('phone_index');
        }

        return $this->render('phone/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/phone/edit/{id}', name: 'phone_edit')]
    public function edit(Request $request, Phone $phone, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(PhoneEditType::class, $phone);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('phone_index');
        }

        return $this->render('phone/edit.html.twig', [
            'form' => $form->createView(),
            'phone' => $phone,
        ]);
    }

    #[Route('/phone/delete/{id}', name: 'phone_delete', methods: ['POST'])]
    public function delete(Request $request, Phone $phone, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$phone->getId(), $request->request->get('_token'))) {
            $entityManager->remove($phone);
            $entityManager->flush();
        }

        return $this->redirectToRoute('phone_index');
    }

    #[Route('/phone/add-to-user/{id}', name: 'phone_add_to_user')]
    public function addToUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $phone = new Phone();
        $phone->setUser($user);

        $form = $this->createForm(PhoneType::class, $phone);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $phoneNumbers = $form->get('phones')->getData();

            foreach ($phoneNumbers as $phoneNumber) {
                $newPhone = new Phone();
                $newPhone->setUser($user);
                $newPhone->setValue($phoneNumber);
                $entityManager->persist($newPhone);
            }

            $entityManager->flush();

            return $this->redirectToRoute('phone_index');
        }

        return $this->render('phone/add_to_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/user/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}