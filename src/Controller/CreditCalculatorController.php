<?php
// src/Controller/CreditCalculatorController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreditCalculatorController extends AbstractController
{
    #[Route('/credit-calculator', name: 'app_credit_calculator')]
    public function index(Request $request): Response
    {
        return $this->render('credit_calculator/index.html.twig');
    }
}