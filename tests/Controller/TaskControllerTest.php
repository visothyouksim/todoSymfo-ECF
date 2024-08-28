<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private function loginUser($client)
{
    $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
    $testUser = $userRepository->findOneByEmail('user@example.com');

    if ($testUser === null) {
        echo "User 'user@example.com' not found in the database.\n";
        exit;
    }

    $this->assertNotNull($testUser, 'User not found in the database.');

    $client->loginUser($testUser);
}





    public function testListTasks()
    {
        $client = static::createClient();
        $this->loginUser($client); // Authentifie un utilisateur

        $crawler = $client->request('GET', '/tasks');

        // Vérifiez le code de statut de la réponse
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifiez le contenu de la réponse
        $this->assertSelectorTextContains('h1', 'Task List');
        $this->assertGreaterThan(0, $crawler->filter('div.task')->count());
    }

    public function testCreateTask()
    {
        $client = static::createClient();
        $this->loginUser($client); // Authentifie un utilisateur

        $crawler = $client->request('GET', '/tasks/new');

        // Vérifiez le code de statut de la réponse
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Soumettez le formulaire
        $form = $crawler->selectButton('Save')->form([
            'task[title]' => 'Test Task',
            'task[description]' => 'This is a test task description',
        ]);

        $client->submit($form);

        // Vérifiez la redirection
        $this->assertResponseRedirects('/tasks');

        // Suivez la redirection et vérifiez la création effective de la tâche
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-success', 'Task created successfully.');
    }

    public function testUpdateTask()
    {
        $client = static::createClient();
        $this->loginUser($client); // Authentifie un utilisateur

        // Supposez qu'une tâche existe déjà avec l'ID 1
        $crawler = $client->request('GET', '/tasks/1/edit');

        // Vérifiez le code de statut de la réponse
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Soumettez le formulaire
        $form = $crawler->selectButton('Update')->form([
            'task[title]' => 'Updated Task Title',
            'task[description]' => 'Updated task description',
        ]);

        $client->submit($form);

        // Vérifiez la redirection
        $this->assertResponseRedirects('/tasks');

        // Suivez la redirection et vérifiez que la tâche a été mise à jour
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-success', 'Task updated successfully.');
    }
}
