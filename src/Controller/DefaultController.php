<?php

namespace App\Controller;

use App\Repository\A2Sport\A2ErabiltzaileakRepository;
use App\Service\SeigarbostApiService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends BaseController
{
    public function __construct(
        private A2ErabiltzaileakRepository $repo,
        private SeigarbostApiService $seigarbostApiService)
    {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->redirectToRoute('openings_index');
    }
}
