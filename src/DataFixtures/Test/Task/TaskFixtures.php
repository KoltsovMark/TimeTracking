<?php

declare(strict_types=1);

namespace App\DataFixtures\Test\Task;

use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Faker\Generator;

class TaskFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    private UserRepository $userRepository;
    private Generator $faker;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->faker = Faker::create();
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->userRepository->findBy(['email' => ['admin@example.com', 'user@example.com']]);

        foreach ($users as $user) {
            for ($i = 0; $i < 15; ++$i) {
                $task = (new Task())
                    ->setTitle($this->faker->text(255))
                    ->setComment($this->faker->paragraph)
                    ->setDate($this->faker->dateTimeBetween('-1 year'))
                    ->setTimeSpent($this->faker->numberBetween(10, 9999))
                    ->setUser($user)
                ;

                $manager->persist($task);
            }

            $manager->flush();
        }
    }

    public static function getGroups(): array
    {
        return ['tests', 'test_tasks'];
    }

    public function getOrder(): int
    {
        return 10;
    }
}
