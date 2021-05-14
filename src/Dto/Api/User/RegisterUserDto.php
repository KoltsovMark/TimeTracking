<?php

declare(strict_types=1);

namespace App\Dto\Api\User;

class RegisterUserDto
{
    private ?string $email = null;
    private ?string $password = null;
    private ?string $passwordConfirmation = null;

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

    public function getPasswordConfirmation(): ?string
    {
        return $this->passwordConfirmation;
    }

    public function setPasswordConfirmation(?string $passwordConfirmation): RegisterUserDto
    {
        $this->passwordConfirmation = $passwordConfirmation;

        return $this;
    }
}
