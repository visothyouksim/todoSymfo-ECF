<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TaskController extends AbstractController
{
    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[Route('/', name: 'app_home')]
    #[Route('/tasks', name: 'app_task_index', methods: ['GET'])]
    public function index(): Response
    {
        $tasks = $this->taskService->getTasksForUser($this->getUser());

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/tasks/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->createTask($task, $this->getUser());

            $this->addFlash('success', 'Task created successfully.');
            return $this->redirectToRoute('app_task_index');
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/tasks/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $task = $this->taskService->getTaskById($id);
        $this->denyAccessUnlessGranted('view', $task);

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $task = $this->taskService->getTaskById($id);
        $this->denyAccessUnlessGranted('edit', $task);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->updateTask($task);

            $this->addFlash('success', 'Task updated successfully.');
            return $this->redirectToRoute('app_task_index');
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/tasks/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        $task = $this->taskService->getTaskById($id);
        $this->denyAccessUnlessGranted('delete', $task);

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $this->taskService->deleteTask($task);
            $this->addFlash('success', 'Task deleted successfully.');
        }

        return $this->redirectToRoute('app_task_index');
    }
}