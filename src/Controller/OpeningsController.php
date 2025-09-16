<?php

namespace App\Controller;

use App\Form\OpeningSearchFormType;
use App\Repository\A2Sport\A2ErabiltzaileakRepository;
use App\Service\SeigarbostApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OpeningsController extends BaseController
{
    public function __construct(
        private A2ErabiltzaileakRepository $repo,
        private SeigarbostApiService $seigarbostApiService)
    {
        
    }

    #[Route('/{_locale}/openings', name: 'openings_index')]
    public function index(Request $request): Response
    {
        $this->loadQueryParameters($request);
        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');
        $dni = $request->query->get('dni');
        
        $aperturas = [];
        $form = $this->createForm(OpeningSearchFormType::class, [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'dni' => $dni,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ( $data['fechaInicio'] !== null && $data['fechaFin'] !== null && $data['dni'] !== null ) {
                $a2Erabiltzailea = $this->repo->findOneByDni($data['dni']);
                if ( $a2Erabiltzailea !== null ) {
                    $aperturas = $this->seigarbostApiService->getAperturas(new \DateTime($data['fechaInicio']), new \DateTime($data['fechaFin']), $a2Erabiltzailea->getTarjetaHex());
                }
            } else {
                  $this->addFlash('error', 'error.form.incomplete');
            }
            // $a2Erabiltzailea = $this->repo->findOneByDni('78866816T');
            // $aperturas = $this->seigarbostApiService->getAperturas(new \DateTime('2023-01-28'), new \DateTime('2023-01-30'), $a2Erabiltzailea->getTarjetaHex());
        }
         return $this->render('openings/index.html.twig', [
            'openings' => $aperturas,
            'a2Erabiltzailea' => $a2Erabiltzailea ?? null,
            'form' => $form,
         ]);
    }
}
