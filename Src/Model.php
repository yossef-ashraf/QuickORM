<?php

namespace ORM\Src;
use ORM\Connection\Connection;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $pdo;
    protected $relations = [];

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
        $this->boot();
    }

    public function find($id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $statement->execute(['id' => $id]);
        $result = $statement->fetch(\PDO::FETCH_OBJ);

        // if ($result) {
        //     foreach ($this->relations as $relation => $details) {
        //         $relatedModel = new $details['model'];
        //         $foreignKey = $details['foreignKey'];
        //         if (method_exists($this, $relation)) {
        //             $result->$relation = $this->$relation()->fetchAll();
        //         }
        //     }
        // }

        return $result;
    }
    public function all()
    {
        $statement = $this->pdo->query("SELECT * FROM {$this->table}");
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }

    public function delete($id)
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $statement->execute(['id' => $id]);
    }

    public function create(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        return $statement->execute($data);
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

    public function with($relations)
    {
        // إذا كان العلاقة عبارة عن سلسلة نصية، قم بتحويلها إلى مصفوفة
        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        foreach ($relations as $relation) {
            $relation = trim($relation);
            $this->relations[$relation] = [
                'model' => $this->getRelatedModel($relation),
                'foreignKey' => $this->getForeignKey($relation),
            ];
        }

        return $this;
    }

    protected function getRelatedModel($relation)
    {
        // افترض أن العلاقة تستخدم اسم النموذج للبحث
        $modelClass = ucfirst($relation);
        return "Model\\$modelClass";
    }

    protected function getForeignKey($relation)
    {
        // افترض أن العلاقة تستخدم معرف النموذج كـ مفتاح أجنبي
        return strtolower($relation) . '_id';
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
        // 
    }

    
    public function __destruct()
    {
        $this->pdo = null;
    }


}
