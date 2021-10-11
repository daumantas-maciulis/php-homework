<?php declare(strict_types=1);

namespace App\Client;

use App\Client\ApiStrategy;


class RapidApiClient implements ApiStrategy
{
    // Readme in https://rapidapi.com/theapiguy/api/free-nba/details
    private string $baseUrl = 'https://free-nba.p.rapidapi.com/';
    private string $key = '4fdb2c4532msh98db9d8739bd30ap17c82cjsn4e79f97aab5a';

    public function fetchData(string $endpoint, ?array $options): array
    {
        $curl = curl_init();

        $url = sprintf('%s%s', $this->baseUrl, $endpoint);

        if($options) {
            $url = sprintf('%s?', $url);
            foreach ($options as $key => $option) {
                $url = sprintf('%s%s&', $url, $option);
            }
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: free-nba.p.rapidapi.com",
                "x-rapidapi-key: " . $this->key
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new \Exception("cURL Error #:" . $err);
        }

        return json_decode($response, true);
    }
}
