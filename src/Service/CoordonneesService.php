<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CoordonneesService
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * Recherche les coordonnées GPS d'une adresse ou lieu.
     *
     * @param string $address L'adresse ou nom du lieu à géocoder
     * @return array|null ['lat' => float, 'lon' => float] ou null si non trouvé
     */
    public function trouverCoord(string $address): ?array
    {
        try {
            $url = 'https://nominatim.openstreetmap.org/search';
            $response = $this->httpClient->request('GET', $url, [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'adressdetails' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'MonAppSymfony/1.0 (+http://monapp.com)', // obligatoire pour Nominatim
                ],
            ]);

            // $content = $response->getContent();

            $data = $response->toArray();

            if (empty($data)) {
                return null;
            }

            return [
                'lat' => (float) $data[0]['lat'],
                'lon' => (float) $data[0]['lon'],
                'display_name' => $data[0]['display_name'] ?? '',
                'adress' => $data[0]['address'] ?? null,
            ];
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            return null;
        }
    }
}
