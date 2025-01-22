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
    // Определяет маршрут для редактирования настроек
    #[Route('/settings', name: 'settings_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Получает репозиторий для сущности Setting
        $settingRepository = $entityManager->getRepository(Setting::class);
        // Находит настройки в базе данных
        $setting = $settingRepository->findOneBy([]);

        // Если настройки не найдены, создает новые
        if (!$setting) {
            $setting = new Setting(); // Создает новый объект настроек
            $setting->setTaxRate(10.00); // Устанавливает налоговую ставку по умолчанию
            $entityManager->persist($setting); // Подготавливает объект для сохранения
            $entityManager->flush(); // Сохраняет изменения в базе данных
        }

        // Создает форму для редактирования настроек
        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request); // Обрабатывает запрос для формы

        // Если форма отправлена и валидна, сохраняет изменения
        if ($form->isSubmitted() && $form->isValid()) {
            // Если объект новый, вызываем persist()
            if (!$setting->getId()) {
                $entityManager->persist($setting);
            }
            $entityManager->flush(); // Сохраняет изменения в базе данных

            // Перенаправляет на страницу редактирования настроек
            return $this->redirectToRoute('settings_edit');
        }

        // Отображает страницу редактирования настроек
        return $this->render('settings/edit.html.twig', [
            'setting' => $setting, // Объект настроек
            'form' => $form->createView(), // Форма для редактирования настроек
        ]);
    }
}