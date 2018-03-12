<?php

namespace App\Command;

use App\Entity\User;
use App\Services\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminCommand extends Command
{
    private $defaultUsername = 'admin';
    private $defaultPassword = 'password';
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:admin:create')
            ->setDescription('Creates a user with ADMIN privileges.')
            ->addArgument('username', InputArgument::OPTIONAL)
            ->addArgument('password', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();

        $user
            ->setEmail($this->getDefaultValueIfNull($input->getArgument('username'), $this->defaultUsername))
            ->setPlainPassword($this->getDefaultValueIfNull($input->getArgument('password'), $this->defaultPassword))
            ->setFirstName('Admin')
            ->setRole('ROLE_ADMIN')
        ;

        $this->userManager->createUser($user);

        $output->writeln(
            ['Admin has been successfully created',
                'username: ' . $user->getUsername(),
                'password: ' . $user->getPlainPassword()]
        );
    }

    private function getDefaultValueIfNull( $enteredValue, $defaultValue)
    {
        return $enteredValue?$enteredValue:$defaultValue;
    }
}