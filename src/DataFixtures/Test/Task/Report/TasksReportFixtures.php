<?php

declare(strict_types=1);

namespace App\DataFixtures\Test\Task\Report;

use App\Entity\Task\TasksReport;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class TasksReportFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    private string $projectDir;
    private UserRepository $userRepository;

    public function __construct(string $projectDir, UserRepository $userRepository)
    {
        $this->projectDir = $projectDir;
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->userRepository->findBy(['email' => ['admin@example.com', 'user@example.com']]);
        $extensions = ['csv', 'pdf', 'xlsx'];

        foreach ($users as $user) {
            foreach ($extensions as $extension) {
                $taskFileName = Uuid::uuid4()->toString().'.'.$extension;
                $task = (new TasksReport())
                    ->setStorage(TasksReport::STORAGE_FILE)
                    ->setStorageType(TasksReport::STORAGE_TYPE_LOCAL)
                    ->setStorageName($taskFileName)
                    ->setStorageFullPath(
                        implode(DIRECTORY_SEPARATOR, [
                            $this->projectDir,
                            'public',
                            $extension,
                            $taskFileName,
                        ])
                    )
                    ->setUser($user)
                ;

                $manager->persist($task);
            }

            $manager->flush();
        }
    }

    public static function getGroups(): array
    {
        return ['tests', 'test_reports'];
    }

    public function getOrder(): int
    {
        return 20;
    }
}
