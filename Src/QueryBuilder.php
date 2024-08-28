<?php

namespace ORM\Src;

class QueryBuilder
{

    protected $pdo;
    protected $query;
    protected $value;
    protected $table;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function select($columns = '*')
    {
        $this->query = "SELECT {$columns} ";
        return $this;
    }

    public function from($table)
    {
        $this->query .= "FROM {$table} ";
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->query .= "WHERE {$column} {$operator} :value ";
        $this->value = $value;
        return $this;
    }

    public function get()
    {
        $statement = $this->pdo->prepare($this->query);
        $statement->execute(['value' => $this->value]);
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }

    public function paginate($perPage, $page)
{
    $offset = ($page - 1) * $perPage;
    $statement = $this->pdo->prepare("SELECT * FROM {$this->table} LIMIT :limit OFFSET :offset");
    $statement->execute(['limit' => $perPage, 'offset' => $offset]);
    return $statement->fetchAll(\PDO::FETCH_OBJ);
}



}
