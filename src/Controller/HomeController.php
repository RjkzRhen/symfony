<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // Определяет маршрут для главной страницы
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        // Отображает главную страницу
        return $this->render('home/index.html.twig');
    }
}