<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Model\User\UseCase\SignUp\Confirm;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'user:confirm',
    description: 'Confirms signed up user.',
)]
class ConfirmCommand extends Command
{
    /** @var UserFetcher */
    private UserFetcher $users;

    /** @var Confirm\Manual\Handler */
    private Confirm\Manual\Handler$handler;

    /**
     * @param UserFetcher $users
     * @param Confirm\Manual\Handler $handler
     */
    public function __construct(UserFetcher $users, Confirm\Manual\Handler $handler)
    {
        $this->users = $users;
        $this->handler = $handler;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $email = $helper->ask($input, $output, new Question('Email: '));

        if (!$user = $this->users->findByEmail($email)) {
            throw new LogicException('User is not found.');
        }

        $command = new Confirm\Manual\Command($user['id']);
        $this->handler->handle($command);

        $output->writeln('<info>Done!</info>');
    }
}