<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function isEmailExist(string $email): bool
    {
        return (bool) $this->userRepository->findByEmail($email);
    }
}
