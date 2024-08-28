<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        // Initialisation du kernel Symfony pour avoir accès au conteneur de services
        self::bootKernel();

        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $this->taskRepository = $this->entityManager->getRepository(Task::class);
    }

    public function testFindUncompletedTasks()
    {
        // Création d'un utilisateur avec un email et un mot de passe
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password123'); // Définition d'un mot de passe
        $this->entityManager->persist($user);

        // Création de quelques tâches de test
        $task1 = new Task();
        $task1->setTitle('Task 1')
            ->setUser($user);
        // Task 1 est incomplète (pas de date de fin)

        $task2 = new Task();
        $task2->setTitle('Task 2')
            ->setUser($user);
        // Task 2 est incomplète (pas de date de fin)

        $this->entityManager->persist($task1);
        $this->entityManager->persist($task2);
        $this->entityManager->flush();

        // Appel de la méthode du repository pour tester
        $tasks = $this->taskRepository->findBy(['dueDate' => null]);

        // Vérification des résultats
        $this->assertCount(2, $tasks);
        $this->assertContainsOnlyInstancesOf(Task::class, $tasks);
    }

    public function testSearchTasksByKeyword()
    {
        // Création d'un utilisateur avec un email et un mot de passe
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password123'); // Définition d'un mot de passe
        $this->entityManager->persist($user);

        // Création de quelques tâches de test
        $task1 = new Task();
        $task1->setTitle('First task for testing')
            ->setUser($user);

        $task2 = new Task();
        $task2->setTitle('Another task for testing')
            ->setUser($user);

        $this->entityManager->persist($task1);
        $this->entityManager->persist($task2);
        $this->entityManager->flush();

        // Utilisation de QueryBuilder pour rechercher par mot-clé partiel
        $query = $this->taskRepository->createQueryBuilder('t')
            ->where('t.title LIKE :keyword')
            ->setParameter('keyword', '%task%')
            ->getQuery();

        $tasks = $query->getResult();

        // Vérification des résultats
        $this->assertCount(2, $tasks);
        $this->assertContainsOnlyInstancesOf(Task::class, $tasks);
    }



    protected function tearDown(): void
    {
        parent::tearDown();

        // Nettoyage de la base de données après les tests
        if ($this->entityManager) {
            $this->entityManager->createQuery('DELETE FROM App\Entity\Task')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();

            // Fermeture de l'entity manager pour éviter les fuites de mémoire
            $this->entityManager->close();
        }

    }
}
