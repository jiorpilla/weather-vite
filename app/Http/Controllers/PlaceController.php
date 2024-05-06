<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;

class PlaceController extends Controller
{
    public $clientId;
    public $clientSecret;
    public $apiKey;
    public $owmApiKey;

    public function __construct()
    {
        $this->clientId = env('FOURSQUARE_CLIENT_ID');
        $this->clientSecret = env('FOURSQUARE_CLIENT_SECRET');
        $this->apiKey = env('FOURSQUARE_API');
        $this->owmApiKey = env('OPENWEATHERMAP_API');
    }

    /**
     * This will search for venues near the 'CITY' the user inputted
     *
     * @param string $search
     * @param Client $client
     * @return false|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function placeSearch(string $search = 'tokyo', Client $client): JsonResponse
    {
        $response = $client->request('GET', 'https://api.foursquare.com/v3/places/search?near=' . $search . ',JP', [
            'verify' => false,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'headers' => [
                'Authorization' => $this->apiKey,
                'accept' => 'application/json',
            ],
        ]);

        $result = $response->getBody();
        $streamContents = $result->getContents();
        $data = json_decode($streamContents, true);

        $venues = [];

        foreach($data['results'] as $place){
            $place['imageUrl'] = $this->getPlaceImage($place['fsq_id'], $client);
            $place['iconUrl'] = $place['categories'][0]['icon']['prefix'] . "32" . $place['categories'][0]['icon']['suffix'];
            $place['categoryName'] = $place['categories'][0]['name'];
            $venues[] = $place;

        }

        $city_weather = $this->getCurrentWeatherByCoordinates($data['context'], $client);
        $city_forecast = $this->getForecastWeatherByCoordinates($data['context'], $client);

        $return = [
            'venues' => $venues,
            'city_weather' => $city_weather,
            'city_forecast' => $city_forecast,
        ];
        return response()->json($return);
    }

    /**
     * This method fetches images url from the API based on the ID of the venues
     *
     * @param string $id
     * @param Client $client
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlaceImage(string $id, Client $client): string
    {
        $response = $client->request('GET', "https://api.foursquare.com/v3/places/$id/photos", [
            'verify' => false,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'headers' => [
                'Authorization' => $this->apiKey,
                'accept' => 'application/json',
            ],
        ]);

        $result = $response->getBody();
        $streamContents = $result->getContents();

        $data = json_decode($streamContents, true);
        $randomizer = rand(0, count($data)-1);
        $imgDimention = "600x400";
        $img_url = $data[$randomizer]['prefix'] . $imgDimention . $data[$randomizer]['suffix'];

        return $img_url;
    }

    /**
     * This method will take current weather based on the coordinates given
     *
     * @param array $context
     * @param Client $client
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCurrentWeatherByCoordinates(array $context, Client $client): array
    {
        $lat  = $context['geo_bounds']['circle']['center']['latitude'];
        $long = $context['geo_bounds']['circle']['center']['longitude'];
        $response = $client->request('GET', "https://api.openweathermap.org/data/2.5/weather?lon={$long}&lat={$lat}&appid={$this->owmApiKey}&units=metric", [
            'verify' => false
        ]);

        $result = $response->getBody();
        $streamContents = $result->getContents();

        $data = json_decode($streamContents, true);

        $output = [
            'description'   => ucwords($data['weather'][0]['description']),
            'iconUrl'       => "https://openweathermap.org/img/wn/" . $data['weather'][0]['icon'] . ".png",
            'main'          => $data['main'],
            'wind'          => $data['wind'],
        ];

        return $output;
    }

    /**
     * This will fetch 5 days of forecast at a fixed time ($output_time)
     *
     * @param array $context
     * @param Client $client
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getForecastWeatherByCoordinates(array $context, Client $client): array
    {
        $lat  = $context['geo_bounds']['circle']['center']['latitude'];
        $long = $context['geo_bounds']['circle']['center']['longitude'];
        $response = $client->request('GET', "https://api.openweathermap.org/data/2.5/forecast?lon={$long}&lat={$lat}&appid={$this->owmApiKey}&units=metric", [
            'verify' => false
        ]);

        $result = $response->getBody();
        $streamContents = $result->getContents();

        $data = json_decode($streamContents, true);

        foreach($data['list'] as $forecast){
            $output_time = '09:00:00';
            if($output_time == substr($forecast['dt_txt'], 11))
            {
                $output[substr($forecast['dt_txt'], 0, 10)] = [
                    'description'   => ucwords($forecast['weather'][0]['description']),
                    'iconUrl'       => "https://openweathermap.org/img/wn/" . $forecast['weather'][0]['icon'] . ".png",
                    'main'          => $forecast['main'],
                    'wind'          => $forecast['wind'],
                    'date'          => date('Y-m-d', strtotime($forecast['dt'])),
                    'date_text'     => substr($forecast['dt_txt'], 0, 16),
                ];
            }
        }
        return $output;
    }
}
