<?php

namespace App\Command;

use App\Service\EtherpadClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EtherpadClientCommand extends Command
{
    protected static $defaultName = 'app:etherpad-client';

    /** @var EtherpadClient */
    private $etherpadClient;

    public function __construct(?string $name = null, EtherpadClient $etherpadClient)
    {
        parent::__construct($name);
        $this->etherpadClient = $etherpadClient;
    }

    protected function configure()
    {
        $this
            ->setDescription('test etherpad client')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $groupId = $this->etherpadClient->createGroup();
        $io->text($groupId);
        $padId = $this->etherpadClient->createGroupPad($groupId);
        $io->text($padId);
        $this->etherpadClient->deleteGroup($groupId);

        return 0;
    }
}
