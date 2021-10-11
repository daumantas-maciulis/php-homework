<?php

namespace App\Client;

class FetchSportsData
{
    public function __construct(
        private ApiStrategy $strategy,
    ){}

    public function setStrategy(ApiStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function getTeams(string $endpoint, array $options): array
    {
        return $this->strategy->fetchData($endpoint, $options);
    }

    public function getGames(string $endpoint, array $options): array
    {
        return $this->strategy->fetchData($endpoint, $options);
    }

    public function getPlayers(string $endpoint, array $options, int $page): array
    {
        array_push($options, 'page=' . $page);

        return $this->strategy->fetchData($endpoint, $options);
    }

}