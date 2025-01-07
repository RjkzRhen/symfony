<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]  // Определение маршрута для регистрации
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response  // Метод для регистрации
    {
        $user = new User();  // Создаем новый объект пользователя
        $form = $this->createForm(RegistrationFormType::class, $user);  // Создаем форму регистрации

        $form->handleRequest($request);  // Обрабатываем запрос для формы
        if ($form->isSubmitted() && $form->isValid()) {  // Если форма отправлена и валидна
            // Хешируем пароль
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()  // Получаем пароль из формы
                )
            );

            // Сохраняем пользователя
            $entityManager->persist($user);  // Подготавливаем объект для сохранения
            $entityManager->flush();  // Сохраняем изменения в базе данных

            // Редирект после успешной регистрации
            $this->addFlash('success', 'Регистрация прошла успешно!');  // Добавляем сообщение об успешной регистрации
            return $this->redirectToRoute('app_login');  // Перенаправляем на страницу входа
        }

        // Отображаем страницу регистрации
        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),  // Передаем форму
        ]);
    }
}