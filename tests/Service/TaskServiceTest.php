<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    private $taskRepository;
    private $entityManager;
    private $taskService;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->taskService = new TaskService($this->taskRepository, $this->entityManager);
    }

    public function testCreateTask()
    {
        $user = new User();
        $task = new Task();

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($task));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->taskService->createTask($task, $user);

        $this->assertSame($user, $task->getUser());
    }

    public function testUpdateTask()
    {
        $task = new Task();

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->taskService->updateTask($task);

    }

    public function testDeleteTask()
    {
        $task = new Task();

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($task));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->taskService->deleteTask($task);
    }
}

