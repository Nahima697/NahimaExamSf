<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'app:delete-user',
    description: 'Commande pour supprimer un salarié',
    aliases: ['app:ds']
)]
class DeleteUserCommand extends Command
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('exitDate', InputArgument::OPTIONAL, "Date de sortie (au format YYYY-MM-DD)")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $exitDate = new \DateTime;

        $userRepository = $this->entityManager->getRepository(User::class);
        $usersToDelete = $userRepository->findBy(['exitDate' => $exitDate]);

        foreach ($usersToDelete as $user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();

        $io->success(count($usersToDelete) . " salarié(s) dont le contrat s'est terminé le " . $exitDate->format('d/m/Y') . " ont bien été supprimés.");

        return Command::SUCCESS;
    }
}


