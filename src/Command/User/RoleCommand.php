<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Model\User\Entity\User\Role as RoleValue;
use App\Model\User\UseCase\Role;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'user:role',
    description: 'Changes user role.',
)]
class RoleCommand extends Command
{
    /** @var UserFetcher */
    private UserFetcher $users;

    /** @var ValidatorInterface */
    private ValidatorInterface $validator;

    /** @var Role\Handler */
    private Role\Handler $handler;

    /**
     * @param UserFetcher $users
     * @param ValidatorInterface $validator
     * @param Role\Handler $handler
     */
    public function __construct(UserFetcher $users, ValidatorInterface $validator, Role\Handler $handler)
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->handler = $handler;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $email = $helper->ask($input, $output, new Question('Email: '));

        if (!$user = $this->users->findByEmail($email)) {
            throw new LogicException('User is not found.');
        }

        $command = new Role\Command($user['id']);

        $roles = [RoleValue::USER, RoleValue::ADMIN];
        $command->role = $helper->ask($input, $output, new ChoiceQuestion('Role: ', $roles, 0));

        $violations = $this->validator->validate($command);

        if ($violations->count()) {
            foreach ($violations as $violation) {
                $output->writeln('<error>' . $violation->getPropertyPath() . ': ' . $violation->getMessage() . '</error>');
            }
            return 1;
        }

        $this->handler->handle($command);

        $output->writeln('<info>Done!</info>');
        return 0;
    }
}