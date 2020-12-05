<?php

namespace App\Command;

use App\Entity\User;
use App\Service\EtherpadClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReloadAuthoridCommand extends Command
{
    protected static $defaultName = 'app:reload-authorid';

    private $em;
    private $etherpadClient;

    /**
     * ReloadAuthoridCommand constructor.
     */
    public function __construct(?string $name = null, EntityManagerInterface $em, EtherpadClient $etherpadClient)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->etherpadClient = $etherpadClient;
    }


    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->em->getRepository(User::class)->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            $authorId = $this->etherpadClient->createAuthor($user->__toString());
            $user->setEtherpadAuthorId($authorId);
        }

        $this->em->flush();

        $io->success('Users author ids have been regenerated');

        return Command::SUCCESS;
    }
}
