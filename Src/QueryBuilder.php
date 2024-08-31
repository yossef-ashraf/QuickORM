<?php

namespace ORM\Src;

class mkdir -p tests/Unit

{
    protected $pdo;
    protected $query;
    protected $value = [];
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
        $this->table = $table;
        $this->query .= "FROM {$table} ";
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->query .= isset($this->value) ? "AND {$column} {$operator} :{$column}" : "WHERE {$column} {$operator} :{$column}";
        $this->value[$column] = $value;
        return $this;
    }

    public function get()
    {
        $statement = $this->pdo->prepare($this->query);
        $statement->execute($this->value);
        $result = $statement->fetchAll(\PDO::FETCH_OBJ);
        $this->resetQuery();
        return $result;
    }

    public function paginate($perPage, $page)
    {
        $offset = ($page - 1) * $perPage;
        $this->query .= " LIMIT :limit OFFSET :offset";
        $statement = $this->pdo->prepare($this->query);
        $statement->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $statement->execute($this->value);
        $result = $statement->fetchAll(\PDO::FETCH_OBJ);
        $this->resetQuery();
        return $result;
    }

    public function query($sql, $params = [])
    {
        $this->query .= $sql;
        if (!empty($params)) {
            $this->value = array_merge($this->value, $params);
        }
        return $this;
    }

    protected function resetQuery()
    {
        $this->query = '';
        $this->value = [];
    }
}
