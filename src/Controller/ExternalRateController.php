<?php

namespace App\Controller;

use App\Entity\ExternalRate;
use App\Entity\ExternalRateRepository;
use App\Form\ExternalRateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/external/rate')]
final class ExternalRateController extends AbstractController
{
    #[Route(name: 'app_external_rate_index', methods: ['GET'])]
    public function index(ExternalRateRepository $externalRateRepository): Response
    {
        return $this->render('external_rate/index.html.twig', [
            'external_rates' => $externalRateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_external_rate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $externalRate = new ExternalRate();
        $form = $this->createForm(ExternalRateType::class, $externalRate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($externalRate);
            $entityManager->flush();

            return $this->redirectToRoute('app_external_rate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('external_rate/new.html.twig', [
            'external_rate' => $externalRate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_external_rate_show', methods: ['GET'])]
    public function show(ExternalRate $externalRate): Response
    {
        return $this->render('external_rate/show.html.twig', [
            'external_rate' => $externalRate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_external_rate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ExternalRate $externalRate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExternalRateType::class, $externalRate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_external_rate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('external_rate/edit.html.twig', [
            'external_rate' => $externalRate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_external_rate_delete', methods: ['POST'])]
    public function delete(Request $request, ExternalRate $externalRate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$externalRate->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($externalRate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_external_rate_index', [], Response::HTTP_SEE_OTHER);
    }
}
