<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    // Определяет маршрут для страницы оплаты
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        // Отображает страницу оплаты
        return $this->render('payment/index.html.twig');
    }
}