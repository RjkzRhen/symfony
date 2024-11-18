<?php

namespace App\Controller;

use App\Entity\UserCsv;
use App\Form\UserCsvType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserCsvController extends AbstractController
{
    #[Route('/user-csv/new', name: 'user_csv_new')]
    public function new(Request $request): Response
    {
        $userCsv = new UserCsv();
        $form = $this->createForm(UserCsvType::class, $userCsv);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->writeToCsv($userCsv);

            $this->addFlash('success', 'User added to CSV successfully!');

            return $this->redirectToRoute('user_csv_new');
        }

        return $this->render('user_csv/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function writeToCsv(UserCsv $userCsv): void
    {
        $filePath = __DIR__ . '/../../exports/userCsv.csv';

        // Если файл ещё не существует, добавляем заголовок
        $fileExists = file_exists($filePath);
        $handle = fopen($filePath, 'a');

        if (!$fileExists) {
            fputcsv($handle, ['Last Name', 'First Name', 'Middle Name', 'Age', 'Username', 'Password']);
        }

        // Добавляем данные
        fputcsv($handle, [
            $userCsv->lastName,
            $userCsv->firstName,
            $userCsv->middleName,
            $userCsv->age,
            $userCsv->username,
            $userCsv->password,
        ]);

        fclose($handle);
    }
}
