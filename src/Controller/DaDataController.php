<?php

namespace App\Controller;

use App\Form\AddressType;
use App\Service\DaDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DaDataController extends AbstractController
{
    private $daDataService;

    // Внедряем сервис DaDataService через конструктор
    public function __construct(DaDataService $daDataService)
    {
        $this->daDataService = $daDataService;
    }

    /**
     * @Route("/suggest-address", name="suggest_address")
     */
    public function suggestAddress(Request $request): Response
    {
        // Создаем форму для ввода адреса
        $form = $this->createForm(AddressType::class);
        $form->handleRequest($request);

        $suggestions = [];

        // Если форма отправлена и валидна
        if ($form->isSubmitted() && $form->isValid()) {
            // Получаем введенный адрес из формы
            $addressQuery = $form->get('address')->getData();
            // Получаем подсказки от сервиса DaData
            $suggestions = $this->daDataService->suggestAddress($addressQuery);
        }

        // Рендерим страницу с формой и подсказками
        return $this->render('dadata/suggest.html.twig', [
            'form' => $form->createView(),
            'suggestions' => $suggestions['suggestions'] ?? [],
        ]);
    }
}