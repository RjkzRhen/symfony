<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Проверяем, что пользователь аутентифицирован полностью
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Получаем текущего пользователя
        $user = $this->getUser();
        if (!$user instanceof User) {
            // Если пользователь не найден или не является экземпляром User, выбрасываем исключение
            throw new \LogicException('Ожидаемый пользователь не найден или имеет неверный тип.');
        }

        // Рендерим страницу профиля, передавая данные пользователя в шаблон
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/update', name: 'app_profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Проверяем, что пользователь аутентифицирован полностью
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Получаем CSRF-токен из запроса
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('update_profile', $submittedToken)) {
            // Если токен невалиден, выбрасываем исключение с сообщением об ошибке
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        // Получаем текущего пользователя
        $user = $this->getUser();

        // Обновляем данные пользователя из запроса
        $user->setLastName($request->request->get('lastName')); // Устанавливаем фамилию
        $user->setFirstName($request->request->get('firstName')); // Устанавливаем имя
        $user->setMiddleName($request->request->get('middleName')); // Устанавливаем отчество
        $user->setAge((int)$request->request->get('age')); // Устанавливаем возраст
        $user->setUsername($request->request->get('username')); // Устанавливаем имя пользователя
        $user->setEmail($request->request->get('email')); // Устанавливаем email

        // Получаем все телефоны из запроса
        $phoneValues = $request->request->all('phones');
        // Удаляем старые телефоны пользователя
        foreach ($user->getPhones() as $phone) {
            $entityManager->remove($phone); // Удаляем телефон из базы данных
        }
        $entityManager->flush(); // Сохраняем изменения в базе данных

        // Добавляем новые телефоны пользователя
        foreach ($phoneValues as $phoneValue) {
            if (!empty($phoneValue)) {
                $phone = new Phone(); // Создаем новый объект Phone
                $phone->setValue($phoneValue); // Устанавливаем значение телефона
                $phone->setUser($user); // Связываем телефон с пользователем
                $entityManager->persist($phone); // Подготавливаем телефон для сохранения в базе данных
            }
        }

        $entityManager->flush(); // Сохраняем изменения в базе данных

        // Добавляем сообщение об успешном обновлении профиля
        $this->addFlash('success', 'Profile updated successfully.');
        // Перенаправляем пользователя на страницу профиля
        return $this->redirectToRoute('app_profile');
    }
}