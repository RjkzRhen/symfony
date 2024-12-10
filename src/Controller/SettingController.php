<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\SettingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingController extends AbstractController
{
    #[Route('/settings', name: 'settings_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $settingRepository = $entityManager->getRepository(Setting::class);
        $setting = $settingRepository->findOneBy([]);

        if (!$setting) {
            $setting = new Setting();
            $setting->setTaxRate(10.00); // Установите налоговую ставку по умолчанию
            $entityManager->persist($setting);
            $entityManager->flush();
        }

        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('settings_edit');
        }

        return $this->render('settings/edit.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }
}