<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Используем атрибут для регистрации команды
#[AsCommand(
    name: 'app:create-third-admin',
    description: 'Создает третьего администратора с именем admin3 и паролем admin789.'
)]
class CreateThirdAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = 'admin3';
        $password = 'admin789';

        // Проверяем, существует ли уже администратор с таким именем
        $existingAdmin = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($existingAdmin) {
            $output->writeln(sprintf('Администратор с именем "%s" уже существует.', $username));
            return Command::FAILURE;
        }

        // Создаем нового администратора
        $admin = new User();
        $admin->setUsername($username);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setLastName('Admin3'); // Добавьте значение для last_name
        $admin->setFirstName('Admin3'); // Добавьте значение для first_name
        $admin->setMiddleName('Admin3'); // Добавьте значение для middle_name
        $admin->setAge(30); // Добавьте значение для age

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $output->writeln(sprintf('Администратор создан: %s / %s', $username, $password));
        return Command::SUCCESS;
    }
}