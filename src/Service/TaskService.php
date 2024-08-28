<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private $taskRepository;
    private $entityManager;

    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    public function createTask(Task $task, User $user): void
    {
        $task->setUser($user);
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function updateTask(Task $task): void
    {
        $this->entityManager->flush();
    }

    public function deleteTask(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function getTasksForUser(User $user): array
    {
        return $this->taskRepository->findBy(['user' => $user]);
    }

    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->find($id);
    }
}