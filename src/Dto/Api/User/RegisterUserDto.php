<?php

declare(strict_types=1);

namespace App\Dto\Api\User;

class RegisterUserDto
{
    private ?string $email;
    private ?string $password;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): RegisterUserDto
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): RegisterUserDto
    {
        $this->password = $password;

        return $this;
    }
}
