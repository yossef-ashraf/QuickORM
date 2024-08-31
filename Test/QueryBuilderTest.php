<?php

namespace ORM\Test;

use PHPUnit\Framework\TestCase;
use ORM\Src\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    protected $queryBuilder;

    protected function setUp(): void
    {
        $pdo = $this->createMock(\PDO::class);
        $this->queryBuilder = new QueryBuilder($pdo);
    }

    public function testSelect()
    {
        $this->queryBuilder->select('*')->from('users');
        $this->assertStringContainsString('SELECT * FROM users', $this->queryBuilder->query);
    }

    public function testWhere()
    {
        $this->queryBuilder->select('*')->from('users')->where('id', '=', 1);
        $this->assertStringContainsString('WHERE id = :id', $this->queryBuilder->query);
        $this->assertArrayHasKey('id', $this->queryBuilder->bindings);
        $this->assertEquals(1, $this->queryBuilder->bindings['id']);
    }

    public function testOrWhere()
    {
        $this->queryBuilder->select('*')->from('users')->where('id', '=', 1)->orWhere('email', '=', 'test@example.com');
        $this->assertStringContainsString('OR email = :email', $this->queryBuilder->query);
        $this->assertArrayHasKey('email', $this->queryBuilder->bindings);
        $this->assertEquals('test@example.com', $this->queryBuilder->bindings['email']);
    }

    public function testPaginate()
    {
        $this->queryBuilder->select('*')->from('users')->paginate(10, 1);
        $this->assertStringContainsString('LIMIT :limit OFFSET :offset', $this->queryBuilder->query);
        $this->assertEquals(10, $this->queryBuilder->bindings['limit']);
        $this->assertEquals(0, $this->queryBuilder->bindings['offset']);
    }

    public function testRawQuery()
    {
        $this->queryBuilder->raw('SELECT * FROM users WHERE id = 1');
        $this->assertStringContainsString('SELECT * FROM users WHERE id = 1', $this->queryBuilder->query);
    }

    protected function tearDown(): void
    {
        $this->queryBuilder = null;
    }
}


