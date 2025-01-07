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
    #[Route('/settings', name: 'settings_edit', methods: ['GET', 'POST'])]  // Определение маршрута для редактирования настроек
    public function edit(Request $request, EntityManagerInterface $entityManager): Response  // Метод для редактирования настроек
    {
        $settingRepository = $entityManager->getRepository(Setting::class);  // Получаем репозиторий для сущности Setting
        $setting = $settingRepository->findOneBy([]);  // Ищем настройки в базе данных

        if (!$setting) {  // Если настройки не найдены, создаем новые
            $setting = new Setting();  // Создаем новый объект настроек
            $setting->setTaxRate(10.00);  // Устанавливаем налоговую ставку по умолчанию
            $entityManager->persist($setting);  // Подготавливаем объект для сохранения
            $entityManager->flush();  // Сохраняем изменения в базе данных
        }

        $form = $this->createForm(SettingType::class, $setting);  // Создаем форму для редактирования настроек
        $form->handleRequest($request);  // Обрабатываем запрос для формы

        if ($form->isSubmitted() && $form->isValid()) {  // Если форма отправлена и валидна
            $entityManager->flush();  // Сохраняем изменения в базе данных

            return $this->redirectToRoute('settings_edit');  // Перенаправляем на страницу редактирования настроек
        }

        // Отображаем страницу редактирования настроек
        return $this->render('settings/edit.html.twig', [
            'setting' => $setting,  // Передаем объект настроек
            'form' => $form->createView(),  // Передаем форму
        ]);
    }
}