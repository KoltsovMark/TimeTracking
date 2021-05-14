<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Dto\Api\User\RegisterUserDto;
use App\Entity\User;
use App\Manager\DoctrineManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationService
{
    private DoctrineManager $manager;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(DoctrineManager $doctrineManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->manager = $doctrineManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function register(RegisterUserDto $registerUserDto): User
    {
        // @todo add possibility to pass a roles
        $user = (new User())->setEmail($registerUserDto->getEmail())
            ->setRoles(['ROLE_API', 'ROLE_USER'])
        ;

        $user->setPassword($this->passwordEncoder->encodePassword($user, $registerUserDto->getPassword()));

        $this->manager->save($user);

        return $user;
    }
}
