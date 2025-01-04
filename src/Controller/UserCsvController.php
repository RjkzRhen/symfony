<?php

namespace App\Controller;

use App\Entity\UserCsv;
use App\Form\UserCsvType;
use App\Form\UserCsvFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserCsvController extends AbstractController
{
    private string $filePath = __DIR__ . '/../../exports/userCsv.csv';

    #[Route('/user-csv', name: 'user_csv_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $filterForm = $this->createForm(UserCsvFilterType::class, null, [
            'method' => 'GET',
        ]);

        $filterForm->handleRequest($request);

        $users = $this->readCsv();

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();

            if ($data['filterField'] && $data['filterValue']) {
                $users = array_filter($users, function ($user) use ($data) {
                    return str_contains($user[$data['filterField']], $data['filterValue']);
                });
            }

            if ($data['sortBy']) {
                usort($users, function ($a, $b) use ($data) {
                    return strcmp($a[$data['sortBy']], $b[$data['sortBy']]) * ($data['sortOrder'] === 'DESC' ? -1 : 1);
                });
            }
        }

        return $this->render('user_csv/index.html.twig', [
            'users' => $users,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    #[Route('/user-csv/new', name: 'user_csv_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $userCsv = new UserCsv();
        $form = $this->createForm(UserCsvType::class, $userCsv);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->writeToCsv($userCsv);
            $this->addFlash('success', 'Пользователь успешно добавлен в CSV!');
            return $this->redirectToRoute('user_csv_index');
        }

        return $this->render('user_csv/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user-csv/{id}/edit', name: 'user_csv_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $users = $this->readCsv();
        if (!isset($users[$id])) {
            throw $this->createNotFoundException('Пользователь не найден');
        }

        $userCsv = (object)$users[$id];
        $form = $this->createForm(UserCsvType::class, $userCsv);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $users[$id] = (array)$userCsv;
            $this->writeCsv($users);
            $this->addFlash('success', 'Пользователь успешно обновлен в CSV!');
            return $this->redirectToRoute('user_csv_index');
        }

        return $this->render('user_csv/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $userCsv,
        ]);
    }

    #[Route('/user-csv/{id}/delete', name: 'user_csv_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $users = $this->readCsv();
            if (isset($users[$id])) {
                unset($users[$id]);
                $this->writeCsv($users);
                $this->addFlash('success', 'Пользователь успешно удален из CSV!');
            }
        }

        return $this->redirectToRoute('user_csv_index');
    }

    private function readCsv(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $file = fopen($this->filePath, 'r');
        $users = [];

        $headers = fgetcsv($file, 0, ';');

        while (($data = fgetcsv($file, 0, ';')) !== false) {
            $user = array_combine($headers, $data);

            // Проверка на наличие всех необходимых ключей
            if (isset($user['Last_Name'], $user['First_Name'], $user['Middle_Name'], $user['Age'], $user['Username'], $user['Password'])) {
                $users[] = $user;
            }
        }

        fclose($file);

        return $users;
    }

    private function writeCsv(array $users): void
    {
        $file = fopen($this->filePath, 'w');

        if (!empty($users)) {
            fputcsv($file, array_keys($users[0]), ';');
            foreach ($users as $user) {
                fputcsv($file, [
                    $user['Last_Name'],
                    $user['First_Name'],
                    $user['Middle_Name'],
                    $user['Age'],
                    $user['Username'],
                    $user['Password'],
                ], ';');
            }
        }

        fclose($file);
    }

    private function writeToCsv(UserCsv $userCsv): void
    {
        $fileExists = file_exists($this->filePath);
        $handle = fopen($this->filePath, 'a');

        if (!$fileExists) {
            fputcsv($handle, ['Last_Name', 'First_Name', 'Middle_Name', 'Age', 'Username', 'Password'], ';');
        }

        fputcsv($handle, [
            $userCsv->Last_Name,
            $userCsv->First_Name,
            $userCsv->Middle_Name,
            $userCsv->Age,
            $userCsv->Username,
            $userCsv->Password,
        ], ';');

        fclose($handle);
    }
}