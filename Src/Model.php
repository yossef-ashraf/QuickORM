<?php 

namespace ORM\Src;

use ORM\Connection\Connection;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $pdo;
    protected $relations = [];
    protected $query = '';
    protected $bindings = [];

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
        $this->boot();
    }

    public function find($id)
    {
        $this->query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $this->bindings = ['id' => $id];
        return $this->first();
    }

    public function all()
    {
        $statement = $this->pdo->query("SELECT * FROM {$this->table}");
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }

    public function first()
    {
        $this->query .= " LIMIT 1";
        $statement = $this->pdo->prepare($this->query);
        $statement->execute($this->bindings);
        $result = $statement->fetch(\PDO::FETCH_OBJ);
        $this->resetQuery();
        return $result;
    }

    public function get()
    {
        $statement = $this->pdo->prepare($this->query);
        $statement->execute($this->bindings);
        $result = $statement->fetchAll(\PDO::FETCH_OBJ);
        $this->resetQuery();
        return $result;
    }

    public function where($column, $operator = '=', $value = null)
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        $this->query .= empty($this->query) ? " WHERE {$column} {$operator} :{$column}" : " AND {$column} {$operator} :{$column}";
        $this->bindings[$column] = $value;
        return $this;
    }

    public function whereNot($column, $operator = '=', $value = null)
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        $this->query .= empty($this->query) ? " WHERE {$column} NOT {$operator} :{$column}" : " AND {$column} NOT {$operator} :{$column}";
        $this->bindings[$column] = $value;
        return $this;
    }

    public function orWhere($column, $operator = '=', $value = null)
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        $this->query .= empty($this->query) ? " WHERE {$column} {$operator} :{$column}" : " OR {$column} {$operator} :{$column}";
        $this->bindings[$column] = $value;
        return $this;
    }

    public function count()
    {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} " . $this->query);
        $statement->execute($this->bindings);
        return $statement->fetch(\PDO::FETCH_OBJ)->count;
    }

    public function paginate($perPage, $page)
    {
        $offset = ($page - 1) * $perPage;
        $this->query .= " LIMIT :limit OFFSET :offset";
        $this->bindings['limit'] = $perPage;
        $this->bindings['offset'] = $offset;
        return $this->get();
    }

    public function create(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        if ($statement->execute($data)) {
            return $this->find($this->pdo->lastInsertId());
        }
        return false;
    }

    public function update(array $data, array $conditions)
    {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ', ');

        $where = '';
        foreach ($conditions as $key => $value) {
            $where .= "{$key} = :{$key}Condition AND ";
        }
        $where = rtrim($where, ' AND ');

        $mergedData = array_merge($data, array_combine(
            array_map(fn($key) => "{$key}Condition", array_keys($conditions)),
            array_values($conditions)
        ));

        $statement = $this->pdo->prepare("UPDATE {$this->table} SET {$fields} WHERE {$where}");
        return $statement->execute($mergedData);
    }

    public function save()
    {
        $data = get_object_vars($this);
        if (isset($this->{$this->primaryKey})) {
            return $this->update($data, [$this->primaryKey => $this->{$this->primaryKey}]);
        } else {
            return $this->create($data);
        }
    }

    public function delete($id)
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $statement->execute(['id' => $id]);
    }

    public function with($relation)
    {
        $this->relations[] = $relation;
        return $this;
    }

    public function hasOne($related, $foreignKey)
    {
        $relatedModel = new $related;
        $statement = $this->pdo->prepare("SELECT * FROM {$relatedModel->table} WHERE {$foreignKey} = :id");
        $statement->execute(['id' => $this->{$this->primaryKey}]);
        return $statement->fetch(\PDO::FETCH_OBJ);
    }

    public function hasMany($related, $foreignKey)
    {
        $relatedModel = new $related;
        $statement = $this->pdo->prepare("SELECT * FROM {$relatedModel->table} WHERE {$foreignKey} = :id");
        $statement->execute(['id' => $this->{$this->primaryKey}]);
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }

    public function belongsTo($related, $foreignKey, $ownerKey = 'id')
    {
        $relatedModel = new $related;
        $statement = $this->pdo->prepare("SELECT * FROM {$relatedModel->table} WHERE {$ownerKey} = :id");
        $statement->execute(['id' => $this->{$foreignKey}]);
        return $statement->fetch(\PDO::FETCH_OBJ);
    }

    public function belongsToMany($related, $pivotTable, $foreignKey, $relatedKey)
    {
        $relatedModel = new $related;
        $statement = $this->pdo->prepare("SELECT {$relatedModel->table}.* FROM {$relatedModel->table}
                                          JOIN {$pivotTable} ON {$relatedModel->table}.{$relatedModel->primaryKey} = {$pivotTable}.{$relatedKey}
                                          WHERE {$pivotTable}.{$foreignKey} = :id");
        $statement->execute(['id' => $this->{$this->primaryKey}]);
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getKeyName()
    {
        return $this->primaryKey;
    }

    public function setKeyName($key)
    {
        $this->primaryKey = $key;
        return $this;
    }

    protected function boot()
    {
        // يتم تنفيذ هذه الدالة عند بناء النموذج
    }

    protected function resetQuery()
    {
        $this->query = '';
        $this->bindings = [];
    }

    public function __destruct()
    {
        $this->pdo = null;
    }
}
