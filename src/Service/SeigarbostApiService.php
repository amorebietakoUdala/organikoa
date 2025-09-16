<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SeigarbostApiService
{

   private string $basicAuth;

   public function __construct(
      private HttpClientInterface $client,
      private string $seigarbostApiUrl,
      private string $seigarbostApiUsername,
      private string $seigarbostApiPassword,
   )
   {
   }
   

   public function getAperturas(\DateTime $fechaInicio, \DateTime $fechaFin, ?string $tarjeta = null, $ordering = '-fecha', $pageNumber = 1)
   {
      /* So lastDate is included in search. Beware, in the URL it will show next day o the selected date but it will work fine */
      $fechaFinSearch = clone $fechaFin;
      $fechaFinSearch->modify('+1 day');
      $params = [
         'fecha_inicio' => $fechaInicio->format('Y-m-d\TH:i:s'),
         'fecha_fin' => $fechaFinSearch->format('Y-m-d\TH:i:s'),
         'ordering' => $ordering,
         'page_number' => $pageNumber,
      ];
      if ($tarjeta !== null) {
         $params['tarjeta'] = $tarjeta;
      }
      $response = $this->operation('aperturas', $params);
      return $response;
   }

   /**
    * El siguiente método realiza una operación genérica contra la API de Seigarbost.
    * Como los resulttados pueden estar paginados, se encarga de ir a buscar todas las páginas aunque en la práctica parece que no será necesario, 
    * puesto que devuelve miles de resultados en una sola página para la operación de aperturas.
    * 
    * @param string $operation
    * @param array $params

    * @return array
    */
   public function operation ($operation, $params = [])
   {
      $allResults = [];
      $totalCount = 0;

      do {
         $response = $this->client->request('GET',$this->seigarbostApiUrl . '/' . $operation,
            [
               'auth_basic' => [$this->seigarbostApiUsername,$this->seigarbostApiPassword],
               'query' => $params
            ]
         );
         $statusCode = $response->getStatusCode();
         if ($statusCode !== 200) {
            throw new \Exception('Error en la llamada a la API de Seigarbost: ' . $statusCode);
         }
         $reponseArray = $response->toArray();
         $totalCount += $reponseArray['totalCount'] ?? 0;
         $allResults = array_merge($allResults, $reponseArray['items'] ?? []); 
         $params['page_number'] += 1;
      } while (isset($responseArray["next"]) && $reponseArray["next"] !== null);

      $results = [];
      $results['totalCount'] = $totalCount;
      $results['next'] = null;
      $results['previous'] = $params['page_number'] - 1 != 1 ? $params['page_number'] - 1 : null;
      $results['items'] = $allResults;
      return $results;
   }

}
