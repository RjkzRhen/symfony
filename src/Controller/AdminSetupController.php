<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminSetupController extends AbstractController
{
    // Определяет маршрут для создания администратора
    #[Route('/setup-admin', name: 'setup_admin')]
    public function createAdmin(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Создает нового пользователя с ролью администратора
        $admin = new User();
        $admin->setUsername('admin'); // Устанавливает имя пользователя
        $admin->setPassword($passwordHasher->hashPassword($admin, 'admin123')); // Хеширует пароль
        $admin->setRoles(['ROLE_ADMIN']); // Устанавливает роль администратора

        // Сохраняет администратора в базе данных
        $entityManager->persist($admin); // Подготавливает объект для сохранения
        $entityManager->flush(); // Сохраняет изменения в базе данных

        // Возвращает сообщение об успешном создании администратора
        return new Response('Администратор создан: admin / admin123');
    }
}