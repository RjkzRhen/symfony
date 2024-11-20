<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Form\PhoneType;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController

{
    #[Route('/phones', name: 'phone_list')]
    public function index(PhoneRepository $phoneRepository): Response
    {
        $phones = $phoneRepository->findAll();

        return $this->render('phone/index.html.twig', [
            'phones' => $phones,
        ]);
    }

    #[Route('/phone/new', name: 'phone_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $phone = new Phone();
        $form = $this->createForm(PhoneType::class, $phone);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($phone);
            $entityManager->flush();

            return $this->redirectToRoute('phone_list');
        }

        return $this->render('phone/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/phone/delete/{id}', name: 'phone_delete', methods: ['POST'])]
    public function delete(Request $request, Phone $phone, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$phone->getId(), $request->request->get('_token'))) {
            $entityManager->remove($phone);
            $entityManager->flush();
        }

        return $this->redirectToRoute('phone_list');
    }


}