<?php

namespace App\Client;

interface ApiStrategy
{
    public function fetchData(string $endpoint, ?array $options): array;
}