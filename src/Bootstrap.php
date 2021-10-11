<?php declare(strict_types=1);

namespace App;

use App\Client\FetchSportsData;
use App\Client\RapidApiClient;
use App\Exception\InvalidCommandException;

class Bootstrap
{
    public function __invoke(array $arguments): void
    {
        array_shift($arguments);
        try {
            if (!isset($arguments[0])) {
                throw new InvalidCommandException('No command specified');
            }

            $command = $arguments[0];
            $commandArguments = array_slice($arguments, 1);

            $this->runCommand($command, $commandArguments);
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }

    }

    private function runCommand(mixed $commandName, array $commandArguments): void
    {
        $command = new Command(new FetchSportsData(new RapidApiClient()));

        switch ($commandName) {
            case 'teams':
                $command->teamsList($commandArguments);
                break;

            case 'help':
                $command->executeHelp();
                break;

            case 'games':
                $command->getGames($commandArguments);
                break;

            default:
                throw new InvalidCommandException('Such command does not exist: ' . $commandName);
        }
    }
}
