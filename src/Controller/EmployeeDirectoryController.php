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
    #[Route('/employee/directory', name: 'employee_directory_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Форма для фильтрации
        $filterForm = $this->createForm(EmployeeDirectoryFilterType::class, null, [
            'method' => 'GET',
        ]);

        // Форма для сортировки
        $sortForm = $this->createForm(EmployeeDirectorySortType::class, null, [
            'method' => 'GET',
        ]);

        $filterForm->handleRequest($request);
        $sortForm->handleRequest($request);

        $queryBuilder = $entityManager->getRepository(EmployeeDirectory::class)->createQueryBuilder('e');

        // Применение фильтрации
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();

            if ($data['filterField'] && $data['filterValue']) {
                $queryBuilder->andWhere('e.' . $data['filterField'] . ' LIKE :filterValue')
                    ->setParameter('filterValue', '%' . $data['filterValue'] . '%');
            }
        }

        // Применение сортировки
        if ($sortForm->isSubmitted() && $sortForm->isValid()) {
            $data = $sortForm->getData();

            if ($data['sortBy']) {
                $queryBuilder->orderBy('e.' . $data['sortBy'], $data['sortOrder'] ?? 'ASC');
            }
        }

        $employees = $queryBuilder->getQuery()->getResult();

        return $this->render('employee_directory/index.html.twig', [
            'employees' => $employees,
            'filterForm' => $filterForm->createView(),
            'sortForm' => $sortForm->createView(),
        ]);
    }

    #[Route('/employee/directory/new', name: 'employee_directory_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $employee = new EmployeeDirectory();
        $form = $this->createForm(EmployeeDirectoryType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('employee_directory_index');
        }

        return $this->render('employee_directory/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/employee/directory/{id}', name: 'employee_directory_show')]
    public function show(EmployeeDirectory $employee): Response
    {
        return $this->render('employee_directory/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/employee/directory/{id}/edit', name: 'employee_directory_edit')]
    public function edit(Request $request, EmployeeDirectory $employee, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(EmployeeDirectoryType::class, $employee);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('employee_directory_index');
        }

        return $this->render('employee_directory/edit.html.twig', [
            'employee' => $employee,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/employee/directory/{id}/delete', name: 'employee_directory_delete', methods: ['POST'])]
    public function delete(Request $request, EmployeeDirectory $employee, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->request->get('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('employee_directory_index');
    }
}