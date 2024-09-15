<?php

namespace ORM\Src;

use ORM\Connection\Connection;
use PDO;
use InvalidArgumentException;

class QueryBuilder
{
    protected $pdo;
    protected $query = '';
    protected $bindings = [];
    protected $table;
    protected $distinct = false;
    protected $joins = [];
    protected $wheres = [];
    protected $orderBys = [];
    protected $limit;
    protected $offset;
    protected $groups = [];
    protected $havings = [];

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function select($columns = '*')
    {
        $this->distinct = false;
        if (is_string($columns)) {
            $columns = explode(',', $columns);
        }
        $this->bindings['select'] = $columns;
        return $this;
    }

    public function selectRaw($expression, array $bindings = [])
    {
        $this->bindings['select'] = array_merge($this->bindings['select'] ?? [], [$expression]);
        $this->addBinding($bindings, 'select');
        return $this;
    }

    public function distinct()
    {
        $this->distinct = true;
        return $this;
    }

    public function from($table)
    {
        $this->table = $table;
        return $this;
    }

    public function join($table, $first, $operator = null, $second = null, $type = 'inner')
    {
        $this->joins[] = [$type, $table, $first, $operator, $second];
        return $this;
    }

    public function leftJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'left');
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $this->where($key, '=', $value, $boolean);
            }
            return $this;
        }

        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = compact('column', 'operator', 'value', 'boolean');
        $this->addBinding($value, 'where');

        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    public function whereIn($column, array $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';
        $this->wheres[] = compact('type', 'column', 'values', 'boolean');
        $this->addBinding($values, 'where');
        return $this;
    }

    public function whereNotIn($column, array $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotBetween' : 'Between';
        $this->wheres[] = compact('type', 'column', 'values', 'boolean');
        $this->addBinding($values, 'where');
        return $this;
    }

    public function whereNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    public function whereNull($column, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';
        $this->wheres[] = compact('type', 'column', 'boolean');
        return $this;
    }

    public function whereNotNull($column, $boolean = 'and')
    {
        return $this->whereNull($column, $boolean, true);
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->orderBys[] = compact('column', 'direction');
        return $this;
    }

    public function limit($value)
    {
        $this->limit = $value;
        return $this;
    }

    public function offset($value)
    {
        $this->offset = $value;
        return $this;
    }

    public function groupBy(...$groups)
    {
        foreach ($groups as $group) {
            $this->groups = array_merge($this->groups, (array) $group);
        }
        return $this;
    }

    public function having($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->havings[] = compact('column', 'operator', 'value', 'boolean');
        $this->addBinding($value, 'having');
        return $this;
    }

    public function orHaving($column, $operator = null, $value = null)
    {
        return $this->having($column, $operator, $value, 'or');
    }

    public function get()
    {
        $sql = $this->toSql();
        $statement = $this->pdo->prepare($sql);
        $this->bindValues($statement);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_OBJ);
        $this->reset();
        return $result;
    }

    public function first()
    {
        $results = $this->limit(1)->get();
        return $results ? $results[0] : null;
    }

    public function paginate($perPage, $columns = '*', $pageName = 'page', $page = null)
    {
        $page = $page ?: ($_GET[$pageName] ?? 1);
        $total = $this->count();
        $results = $this->forPage($page, $perPage)->get();

        return (object) [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max((int) ceil($total / $perPage), 1),
            'from' => $total ? ($page - 1) * $perPage + 1 : null,
            'to' => $total ? min($page * $perPage, $total) : null,
        ];
    }

    public function count($columns = '*')
    {
        $without = ['orders', 'limit', 'offset'];
        $query = $this->cloneWithout($without);
        $query->bindings['select'] = ['COUNT(' . $this->wrap($columns) . ')'];
        return (int) $query->first()->{'COUNT(' . $this->wrap($columns) . ')'};
    }

    public function exists()
    {
        return $this->count() > 0;
    }

    public function toSql()
    {
        $sql = [];

        if (!empty($this->bindings['select'])) {
            $sql[] = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '') . $this->columnize($this->bindings['select']);
        }

        if ($this->table) {
            $sql[] = 'FROM ' . $this->wrap($this->table);
        }

        foreach ($this->joins as $join) {
            $sql[] = $this->compileJoin($join);
        }

        $sql = array_merge($sql, $this->compileWheres());

        if (!empty($this->groups)) {
            $sql[] = 'GROUP BY ' . $this->columnize($this->groups);
        }

        if (!empty($this->havings)) {
            $sql = array_merge($sql, $this->compileHavings());
        }

        if (!empty($this->orderBys)) {
            $sql[] = 'ORDER BY ' . implode(', ', array_map(function ($orderBy) {
                return $this->wrap($orderBy['column']) . ' ' . $orderBy['direction'];
            }, $this->orderBys));
        }

        if ($this->limit) {
            $sql[] = 'LIMIT ' . (int) $this->limit;
        }

        if ($this->offset) {
            $sql[] = 'OFFSET ' . (int) $this->offset;
        }

        return implode(' ', $sql);
    }

    protected function compileJoin($join)
    {
        $table = $this->wrap($join[1]);
        $first = $this->wrap($join[2]);
        $condition = $join[3];
        $second = $join[4];
        $type = strtoupper($join[0]);

        return "$type JOIN $table ON $first $condition $second";
    }

    protected function compileWheres()
    {
        if (empty($this->wheres)) {
            return [];
        }

        $sql = ['WHERE'];

        foreach ($this->wheres as $i => $where) {
            $sql[] = $i === 0 ? '' : $where['boolean'];

            if ($where['type'] === 'In') {
                $sql[] = $this->wrap($where['column']) . ' IN (' . $this->parameterize($where['values']) . ')';
            } elseif ($where['type'] === 'NotIn') {
                $sql[] = $this->wrap($where['column']) . ' NOT IN (' . $this->parameterize($where['values']) . ')';
            } elseif ($where['type'] === 'Null') {
                $sql[] = $this->wrap($where['column']) . ' IS NULL';
            } elseif ($where['type'] === 'NotNull') {
                $sql[] = $this->wrap($where['column']) . ' IS NOT NULL';
            } elseif ($where['type'] === 'Between') {
                $sql[] = $this->wrap($where['column']) . ' BETWEEN ? AND ?';
            } elseif ($where['type'] === 'NotBetween') {
                $sql[] = $this->wrap($where['column']) . ' NOT BETWEEN ? AND ?';
            } else {
                $sql[] = $this->wrap($where['column']) . ' ' . $where['operator'] . ' ?';
            }
        }

        return $sql;
    }

    protected function compileHavings()
    {
        $sql = ['HAVING'];

        foreach ($this->havings as $i => $having) {
            $sql[] = $i === 0 ? '' : $having['boolean'];
            $sql[] = $this->wrap($having['column']) . ' ' . $having['operator'] . ' ?';
        }

        return $sql;
    }

    protected function wrap($value)
    {
        if (stripos($value, ' as ') !== false) {
            $segments = explode(' ', $value);
            return $this->wrap($segments[0]) . ' AS ' . $this->wrap($segments[2]);
        }

        return '`' . str_replace('.', '`.`', $value) . '`';
    }

    protected function parameterize(array $values)
    {
        return implode(', ', array_fill(0, count($values), '?'));
    }

    protected function columnize(array $columns)
    {
        return implode(', ', array_map([$this, 'wrap'], $columns));
    }

    protected function addBinding($value, $type = 'where')
    {
        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type] ?? [], $value));
        } else {
            $this->bindings[$type][] = $value;
        }

        return $this;
    }

    protected function bindValues($statement)
    {
        foreach ($this->getBindings() as $key => $value) {
            $statement->bindValue(
                is_string($key) ? $key : $key + 1,
                $value,
                is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
    }

    public function getBindings()
    {
        return array_merge(...array_values($this->bindings));
    }

    protected function reset()
    {
        $this->bindings = [];
        $this->wheres = [];
        $this->joins = [];
        $this->orders = [];
        $this->limit = null;
        $this->offset = null;
        $this->groups = [];
        $this->havings = [];
    }

    public function forPage($page, $perPage)
    {
        return $this->offset(($page - 1) * $perPage)->limit($perPage);
    }

    protected function cloneWithout(array $properties)
    {
        $clone = clone $this;

        foreach ($properties as $property) {
            $clone->$property = null;
        }

        return $clone;
    }

    public function __clone()
    {
        $this->bindings = array_map(function ($binding) {
            return is_object($binding) ? clone $binding : $binding;
        }, $this->bindings);
    }
}