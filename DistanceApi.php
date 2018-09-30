<?php

namespace App\Services\GoogleApi;

use Exception;
use GuzzleHttp\Client;

/**
 * @author pnlinh
 *
 * Class DistanceApi
 *
 * @package App\Services\GoogleApi
 */
class DistanceApi
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    private $api_url = 'https://maps.googleapis.com/maps/api/distancematrix/json';

    /** @var \Illuminate\Config\Repository|mixed */
    private $apiKey;

    /**
     * DistanceApi constructor.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->apiKey = config('google_api.key');
    }

    /**
     * @param $origins
     * @param $destinations
     * @return int
     */
    public function caculateDistance($origins, $destinations)
    {
        try {
            $response = $this->client->get($this->api_url, [
                'query' => [
                    'units' => 'imperial',
                    'origins' => $origins,
                    'destinations' => $destinations,
                    'key' => $this->apiKey,
                    'random' => random_int(1, 100),
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if (200 === $statusCode) {
                $responseData = json_decode($response->getBody()->getContents());

                if (isset($responseData->rows[0]->elements[0]->distance)) {
                    return $responseData->rows[0]->elements[0]->distance->value;
                }
            }

            return -1;
        } catch (Exception $e) {
            return -1;
        }
    }
}