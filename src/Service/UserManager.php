<?php

namespace App\Service;

use App\DTO\UserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function registerUser(UserDto $userDto): void
    {
        $user = new User();
        $user->setEmail($userDto->getEmail());
        $user->setFullName($userDto->getFullName());

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $userDto->getPlainPassword()
        );

        $user->setApiKey(hash('sha256', $hashedPassword));
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function isUserExistByEmail(string $email): bool
    {
        return is_null($this->userRepository->getUserByEmail($email)) === false;
    }

    public function isPlanPasswordAndRepeatedPasswordAreEqual(string $password, string $repeatedPassword): bool
    {
        return $password === $repeatedPassword;
    }
}