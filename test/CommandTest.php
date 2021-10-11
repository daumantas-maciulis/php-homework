<?php

namespace AppTest;

use App\Client\FetchSportsData;
use App\Command;
use App\Service\Storage;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    public function testGetTeamsList(): void
    {
        $client = $this->createStub(FetchSportsData::class);

        $arguments = ['Boston'];
        $expected = 'Team name: Boston Celtics from Boston';

        $client->method('getTeams')
            ->willReturn($this->returnTeams());

        $command = new Command($client);
        $command->teamsList($arguments);

        $this->expectOutputString($expected);
    }

    public function testSavePlayersToStorage(): void
    {
        $client = $this->createStub(FetchSportsData::class);
        $client->method('getPlayers')
            ->willReturn($this->returnPlayers());

        $command = new Command($client);
        $command->savePlayers(['Lakers']);

        $storage = new Storage();
        $playersFromStorage = $storage->read('players');

        $expected = [
            'Lakers' => [
                'LeBron James'
            ]
        ];

        $this->assertSame($expected, $playersFromStorage);
    }

    public function testGetGames(): void
    {
        $client = $this->createStub(FetchSportsData::class);
        $arguments = [
            0 => '2018-03-21'
        ];
        $client->method('getGames')
            ->willReturn($this->returnGames());

        $client->method('getPlayers')
            ->willReturn($this->returnPlayers());

        $command = new Command($client);
        $command->getGames($arguments);

        $this->expectOutputString(print_r($this->expectedGetGamesReturn(), true));
    }

    private function returnPlayers(): array
    {
        return [
            'data' => [
                [
                    'first_name' => 'LeBron',
                    'last_name' => 'James',
                    'team' => [
                        'name' => 'Lakers'
                    ]
                ],
                [
                    'first_name' => 'Billy',
                    'last_name' => 'Preston',
                    'team' => [
                        'name' => 'Cavaliers'
                    ]
                ],
                [
                    'first_name' => 'Lorenzo',
                    'last_name' => 'Brown',
                    'team' => [
                        'name' => 'Raptors'
                    ]
                ]
            ],
            'meta' => [
                'total_pages' => 1
            ]
        ];
    }

    private function returnTeams(): array
    {
        return [
            'data' => [
                [
                    'id' => 1,
                    'full_name' => 'Atlanta Hawks',
                    'city' => 'Atlanta'
                ],
                [
                    'id' => 2,
                    'full_name' => 'Boston Celtics',
                    'city' => 'Boston'
                ],

            ]
        ];
    }

    private function returnGames(): array
    {
        return [
            'data' => [
                [
                    'home_team_score' => 100,
                    'visitor_team_score' => 99,
                    'home_team' => [
                        'conference' => 'East',
                        'full_name' => 'Cleveland Cavaliers',
                        'name' => 'Cavaliers'
                    ],
                    'visitor_team' => [
                        'conference' => 'East',
                        'full_name' => 'Toronto Raptors',
                        'name' => 'Raptors'
                    ]
                ]
            ]
        ];
    }

    private function expectedGetGamesReturn(): array
    {
        return [
            'result' => '100:99',
            'homeTeam' => [
                'conference' => 'East',
                'full_name' => 'Cleveland Cavaliers',
                'name' => 'Cavaliers',
                'players' => [
                    'Billy Preston'
                ]
            ],
            'visitorTeam' => [
                'conference' => 'East',
                'full_name' => 'Toronto Raptors',
                'name' => 'Raptors',
                'players' => [
                    'Lorenzo Brown'
                ]
            ],
        ];


    }
}