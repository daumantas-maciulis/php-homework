<?php declare(strict_types=1);

namespace App;

use App\Client\FetchSportsData;
use App\Exception\BadDateException;
use App\Exception\NoGamesPlayedException;
use App\Service\Printer;
use App\Service\Storage;

class Command
{
    private Printer $printer;

    private Storage $storage;

    private FetchSportsData $fetchSportsData;

    public function __construct(FetchSportsData $fetchSportsData){
        $this->fetchSportsData = $fetchSportsData;
        $this->printer = new Printer();
        $this->storage = new Storage();
    }

    public function executeHelp(): void
    {
        $this->printer->writeLn('Command: teams <team-keyword>');
        $this->printer->writeLn(str_pad(' ', 4) .'List NBA teams');
        $this->printer->writeLn('');

        $this->printer->writeLn('Command: games <game-date>');
        $this->printer->writeLn(str_pad(' ', 4) .'List games for specific date where home team won and both teams belong to same conference');
        $this->printer->writeLn('');
    }

    public function teamsList(array $arguments): void
    {
        $endpoint = 'teams';
        $options = ['page=0'];
        $teamFound = false;

        $teams = $this->fetchSportsData->getTeams($endpoint, $options);
        $filter = $arguments[0] ?? null;

        foreach ($teams['data'] as $team) {
            if ($filter !== null && (stristr($team['full_name'], $filter) || stristr($team['city'], $filter))) {
                $this->printer->write('Team name: ' . $team['full_name'] . ' from ' . $team['city']);
                $teamFound = true;
            }
        }
        if(!$teamFound && $arguments[0]) {
            $this->printer->write('No teams were found with name: ' . $arguments[0]);
        }
    }

    public function getGames(array $arguments): void
    {
        $options = [];
        $teamsPlayed = [];
        $gameIds = [];
        $endpoint = 'games';

        if ($arguments[0]) {
            if (preg_match('([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))', $arguments[0])) {
                array_push($options, sprintf('dates[]=%s', $arguments[0]));
            } else {
                throw new BadDateException('Please provide valid date. Format Y-m-d');
            }
        }

        $games = $this->fetchSportsData->getGames($endpoint, $options);

        if (!$games['data']) {
            throw new NoGamesPlayedException('No games were played this day');
        }

        foreach ($games['data'] as $game) {
            if ($game['home_team_score'] > $game['visitor_team_score'] && $game['home_team']['conference'] === $game['visitor_team']['conference']) {

                array_push($teamsPlayed, $game['home_team']['name'], $game['visitor_team']['name']);
                $thisGameId = uniqid($arguments[0] . '-');
                array_push($gameIds, $thisGameId);

                $gameData = [
                    'result' => $game['home_team_score'] . ':' . $game['visitor_team_score'],
                    'homeTeam' => $game['home_team'],
                    'visitorTeam' => $game['visitor_team'],
                ];

                $this->storage->write($thisGameId, $gameData);
            }
        }

        $this->savePlayers($teamsPlayed);
        $teamPlayers = $this->storage->read('players');

        foreach ($gameIds as $savedGame) {
            $game = $this->storage->read($savedGame);
            $game['homeTeam']['players'] = $teamPlayers[$game['homeTeam']['name']];
            $game['visitorTeam']['players'] = $teamPlayers[$game['visitorTeam']['name']];

            $this->printer->writeArr($game);
            $this->storage->remove($savedGame);
        }
        $this->storage->remove('players');
    }

    public function savePlayers(array $teamsPlayed): void
    {
        $options = ['per_page=100'];
        $endpoint = 'players';
        $totalPages = 2;

        $teamPlayers = [];

        foreach ($teamsPlayed as $teamPlayed) {
            $teamPlayers[$teamPlayed] = [];
        }


        for ($i = 1; $i <= $totalPages; $i++) {
            $page = $i;
            $players = $this->fetchSportsData->getPlayers($endpoint, $options, $page);
            $totalPages = $players['meta']['total_pages'];

            foreach ($players['data'] as $player) {
                if (in_array($player['team']['name'], $teamsPlayed)) {
                    array_push($teamPlayers[$player['team']['name']], $player['first_name'] . ' ' . $player['last_name']);
                }
            }
        }
        $this->storage->write('players', $teamPlayers);
    }
}
