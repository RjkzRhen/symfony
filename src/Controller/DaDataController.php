<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Velhron\DadataBundle\Service\DadataSuggest;

class DaDataController extends AbstractController
{
    private $dadataSuggest;

    public function __construct(DadataSuggest $dadataSuggest)
    {
        $this->dadataSuggest = $dadataSuggest;
    }

    /**
     * @Route("/suggest-address", name="suggest_address", methods={"GET"})
     */
    public function suggestAddress(Request $request): JsonResponse
    {
        $query = $request->query->get('query', '');

        if (empty($query)) {
            return $this->json(['error' => 'Query is required'], 400);
        }

        $response = $this->dadataSuggest->suggestAddress($query, ['count' => 5]);

        return $this->json($response);
    }

    /**
     * @Route("/suggest-party", name="suggest_party", methods={"GET"})
     */
    public function suggestParty(Request $request): JsonResponse
    {
        $query = $request->query->get('query', '');

        if (empty($query)) {
            return $this->json(['error' => 'Query is required'], 400);
        }

        $response = $this->dadataSuggest->suggestParty($query, ['count' => 5]);

        return $this->json($response);
    }
}