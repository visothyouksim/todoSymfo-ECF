<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class TaskTest extends TestCase
{
    public function testTaskCreation()
    {
        $task = new Task();

        $this->assertInstanceOf(Task::class, $task);
    }

    public function testTaskGettersAndSetters()
    {
        $task = new Task();
        $user = new User();

        $task->setTitle('Test Task');
        $task->setDescription('This is a test description');
        $task->setDueDate(new \DateTime('2024-12-31'));
        $task->setUser($user);

        $this->assertEquals('Test Task', $task->getTitle());
        $this->assertEquals('This is a test description', $task->getDescription());
        $this->assertEquals('2024-12-31', $task->getDueDate()->format('Y-m-d'));
        $this->assertInstanceOf(User::class, $task->getUser());
        $this->assertEquals($user, $task->getUser());
    }

    public function testValidationConstraints()
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $task = new Task();

        $task->setTitle('');
        $errors = $validator->validate($task);

        $this->assertGreaterThan(0, count($errors), 'An empty title should generate validation errors.');

        $task->setTitle('Valid Title');
        $errors = $validator->validate($task);

        $this->assertEquals(0, count($errors), 'A valid title should not generate validation errors.');

        $task->setDueDate(null);
        $errors = $validator->validate($task);

        $this->assertEquals(0, count($errors), 'A null due date should not generate validation errors.');
    }
}
