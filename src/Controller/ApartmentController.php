<?php


namespace App\Controller;

use App\Entity\Apartment;
use App\Form\ApartmentType;
use App\Repository\ApartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/apartment')]
class ApartmentController extends AbstractController
{
    #[Route('/', name: 'apartment_index', methods: ['GET'])]
    public function index(ApartmentRepository $apartmentRepository): Response
    {
        return $this->render('apartment/index.html.twig', [
            'apartments' => $apartmentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'apartment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $apartment = new Apartment();
        $form = $this->createForm(ApartmentType::class, $apartment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($apartment);
            $entityManager->flush();

            return $this->redirectToRoute('apartment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('apartment/new.html.twig', [
            'apartment' => $apartment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'apartment_show', methods: ['GET'])]
    public function show(Apartment $apartment): Response
    {
        return $this->render('apartment/show.html.twig', [
            'apartment' => $apartment,
        ]);
    }

    #[Route('/{id}/edit', name: 'apartment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Apartment $apartment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApartmentType::class, $apartment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('apartment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('apartment/edit.html.twig', [
            'apartment' => $apartment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'apartment_delete', methods: ['POST'])]
    public function delete(Request $request, Apartment $apartment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$apartment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($apartment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('apartment_index', [], Response::HTTP_SEE_OTHER);
    }
}