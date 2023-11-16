<?php

namespace App\Command;

use App\Entity\AcquisitionSystem;
use App\Entity\Room;
use App\Entity\Sensor;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:purge-database',
    hidden: false
)]
class PurgerDatabaseCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Purges the database')
            ->setHelp('This command allows you to purge the database for development purposes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Purging the database');

        $this->entityManager->createQueryBuilder()->delete(Sensor::class)->getQuery()->execute();
        $this->entityManager->createQueryBuilder()->delete(AcquisitionSystem::class)->getQuery()->execute();
        $this->entityManager->createQueryBuilder()->delete(User::class)->getQuery()->execute();
        $this->entityManager->createQueryBuilder()->delete(Room::class)->getQuery()->execute();

        $this->entityManager->flush();

        $io->success('Database purged successfully.');

        return Command::SUCCESS;
    }
}