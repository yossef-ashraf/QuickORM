<?php

namespace ORM\Test;

use PHPUnit\Framework\TestCase;
use ORM\Src\Model;
use ORM\Connection\Connection;

class ModelTest extends TestCase
{
    protected $model;

    protected function setUp(): void
    {
        $this->model = $this->getMockForAbstractClass(Model::class);
        $pdo = $this->createMock(\PDO::class);
        $this->model->pdo = $pdo;
    }

    public function testFind()
    {
        $statement = $this->createMock(\PDOStatement::class);
        $statement->method('fetch')->willReturn((object) ['id' => 1, 'name' => 'Test']);

        $this->model->pdo->method('prepare')->willReturn($statement);
        
        $result = $this->model->find(1);

        $this->assertEquals(1, $result->id);
        $this->assertEquals('Test', $result->name);
    }

    public function testCreate()
    {
        $statement = $this->createMock(\PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $this->model->pdo->method('prepare')->willReturn($statement);
        $this->model->pdo->method('lastInsertId')->willReturn(1);
        
        $data = ['name' => 'Test', 'email' => 'test@example.com'];
        $result = $this->model->create($data);

        $this->assertEquals(1, $result->id);
    }

    public function testUpdate()
    {
        $statement = $this->createMock(\PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $this->model->pdo->method('prepare')->willReturn($statement);

        $data = ['name' => 'Updated'];
        $conditions = ['id' => 1];

        $result = $this->model->update($data, $conditions);

        $this->assertTrue($result);
    }

    public function testDelete()
    {
        $statement = $this->createMock(\PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $this->model->pdo->method('prepare')->willReturn($statement);

        $result = $this->model->delete(1);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->model = null;
    }
}

