<?php

namespace App\Controller;

use App\Form\OpeningSearchFormType;
use App\Repository\A2Sport\A2ErabiltzaileakRepository;
use App\Service\SeigarbostApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OpeningsController extends BaseController
{
    public function __construct(
        private A2ErabiltzaileakRepository $repo,
        private SeigarbostApiService $seigarbostApiService,
        private TranslatorInterface $translator,
    )
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
            if ( $data['fechaInicio'] !== null && $data['fechaFin'] !== null && ( $data['dni'] !== null || $data['tarjeta'] !== null) ) {
                if ( $data['dni'] !== null) {
                    $a2Erabiltzailea = $this->repo->findOneByDni($data['dni']);
                } else {
                    $a2Erabiltzailea = $this->repo->findOneByTarjeta($data['tarjeta']);
                }
                if ( $a2Erabiltzailea !== null ) {
                    $aperturas = $this->seigarbostApiService->getAperturas(new \DateTime($data['fechaInicio']), new \DateTime($data['fechaFin']), $a2Erabiltzailea->getTarjetaHex());
                } else {
                    dump($this->getTarjetaHex($data['tarjeta']));
                    // 'message.cardNotRegisteredInA2Sport'
                    $this->addFlash('error', $this->translator->trans('message.cardNotRegisteredInA2Sport',[
                        '{cardNumberDecimal}' => $data['tarjeta'],
                        '{cardNumberHex}' => $this->getTarjetaHex($data['tarjeta']),
                    ]));  
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

    private function getTarjetaHex(string $tarjeta): ?string
    {
        $hexadecimal = $this->bigzToHexReversed($tarjeta);
        return mb_strtoupper($hexadecimal);
    }

    private static function bigzToHexReversed(string|int $number): string
    {
        $number = ltrim((string)$number, '0'); // Quitar ceros iniciales
        if (!ctype_digit($number)) {
            throw new \InvalidArgumentException("El valor debe ser un número positivo.");
        }
        $hex = dechex((int)$number);
        if (strlen($hex) % 2 !== 0) {
            $hex = '0' . $hex;
        }
        $octets = str_split($hex, 2);
        $reversedHex = implode('', array_reverse($octets));
        return $reversedHex ?: '00';
    }    


}
