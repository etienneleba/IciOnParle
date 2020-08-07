<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SimulatorCommand extends Command
{
    protected static $defaultName = 'app:simulator';

    public function nextStep(SymfonyStyle $io, int $step, int $users, int $minUsersPerGroup, int $usersFinalGroup, array $result): array
    {
        $groups = $users > $usersFinalGroup ? floor($users / $minUsersPerGroup) : 1;

        if (1 == $groups) {
            $users = $usersFinalGroup;
        }

        $result[] = [$step, $users, $groups];

        if (1 == $groups) {
            return $result;
        }

        return $this->nextStep($io, $step + 1, $groups, $minUsersPerGroup, $usersFinalGroup, $result);
    }

    protected function configure()
    {
        $this
            ->setDescription('Simulate a deliberative assembly event')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Deliberative assembly event simulator');
        $io->newLine(2);

        $users = $io->ask('How many users for this event ?', 100);
        $minUsersPerGroup = $io->ask('How many users per group ?', 5);
        $usersFinalGroup = $io->ask('How many users for the final group ?', 5);

        $io->text('Participants : '.$users);
        $io->text('Min users per group : '.$minUsersPerGroup);
        $io->text('Participants in the final group  : '.$usersFinalGroup);

        $io->newLine(1);

        $io->table(
            ['Step', 'Participants', 'Groups'],
            $this->nextStep($io, 1, $users, $minUsersPerGroup, $usersFinalGroup, [])
        );

        $io->success('The simulation went well !');

        return 0;
    }
}
