<?php

declare(strict_types=1);

namespace App\DataFixtures\Test\User;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $usersArray = [
            [
                'email' => 'admin@example.com',
                'password' => '12345qwerty',
                'roles' => ['ROLE_API', 'ROLE_ADMIN'],
            ],
            [
                'email' => 'user@example.com',
                'password' => '12345qwerty',
                'roles' => ['ROLE_API', 'ROLE_USER'],
            ],
            [
                'email' => 'userNew@example.com',
                'password' => '12345qwerty',
                'roles' => ['ROLE_API', 'ROLE_USER'],
            ],
        ];

        foreach ($usersArray as $userArray) {
            $user = (new User())->setEmail($userArray['email'])
                ->setRoles($userArray['roles'])
            ;
            $user->setPassword($this->encoder->encodePassword($user, $userArray['password']));
            $manager->persist($user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['tests', 'test_users'];
    }

    public function getOrder(): int
    {
        return 1;
    }
}
