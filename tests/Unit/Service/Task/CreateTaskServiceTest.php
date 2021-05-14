<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Task;

use App\Dto\Api\Form\Task\CreateTaskTypeDto;
use App\Entity\Task\Task;
use App\Entity\User;
use App\Factory\Api\Task\Dto\CreateTaskDtoFactory;
use App\Manager\DoctrineManager;
use App\Service\Task\CreateTaskService;
use App\Tests\Unit\Traits\AccessiblePrivatePropertyTrait;
use DateTime;
use PHPUnit\Framework\TestCase;

class CreateTaskServiceTest extends TestCase
{
    use AccessiblePrivatePropertyTrait;

    protected DoctrineManager $doctrineManagerMock;
    protected CreateTaskDtoFactory $createTaskDtoFactory;

    /**
     * @covers \App\Service\Task\CreateTaskService::createTask
     *
     * @dataProvider dataProviderForCreateTask
     */
    public function testCreateTask(
        string $title,
        string $comment,
        int $timeSpent,
        DateTime $date,
        User $user
    ) {
        $createTaskTypeDto = (new CreateTaskTypeDto())
            ->setTitle($title)
            ->setComment($comment)
            ->setTimeSpent($timeSpent)
            ->setDate($date)
        ;

        $createTaskDto = $this->createTaskDtoFactory->createFromCreateTaskTypeDto($createTaskTypeDto, $user);

        $expectedTask = (new Task())->setTitle($createTaskDto->getTitle())
            ->setComment($createTaskDto->getComment())
            ->setTimeSpent($createTaskDto->getTimeSpent())
            ->setDate($createTaskDto->getDate())
            ->setUser($createTaskDto->getUser())
        ;

        $this->doctrineManagerMock
            ->expects($this->once())
            ->method('save')
            ->with($expectedTask)
            ->willReturn($expectedTask)
        ;

        $task = $this->getCreateTaskService()->createTask($createTaskDto);

        $this->assertSame($expectedTask, $task);
    }

    public function dataProviderForCreateTask(): array
    {
        return [
            [
                'title' => 'title',
                'comment' => 'comment content',
                'time_spent' => 123,
                'date' => new DateTime(),
                'user' => new User(),
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createTaskDtoFactory = new CreateTaskDtoFactory();

        $this->doctrineManagerMock = $this->createMock(DoctrineManager::class);
    }

    protected function getCreateTaskService(): CreateTaskService
    {
        return new CreateTaskService($this->doctrineManagerMock);
    }
}
