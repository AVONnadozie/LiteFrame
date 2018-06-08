<?php

namespace LiteFrame\Database;

use Closure;
use Exception;
use ReflectionObject;
use ReflectionProperty;

class Model extends QueryBuilder
{
    protected static $connection;
    private $record;
    protected $primary = 'id';
    private $primaryValues;
    protected $table;
    protected $wheres = [];
    private $final;

    public function __construct($id = null)
    {
        $this->load($id);
    }

    public function getTable()
    {
        if (empty($this->table)) {
            $classname = get_class($this);
            $split = explode('\\', $classname);
            $name = array_pop($split);
            $this->table = pluralize(strtolower($name));
        }

        return $this->table;
    }

    public static function getConnection()
    {
        if (!isset(static::$connection)) {
            $db = config('database');
            static::$connection = mysqli_connect($db['host'], $db['dbuser'], $db['dbpassword'], $db['dbname'], $db['port']);
        }

        return static::$connection;
    }

    private function setProperties($record = null)
    {
        if ($record) {
            $this->record = $record;
        }

        if ($this->record) {
            foreach ($this->record as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function isEmpty()
    {
        return empty($this->record) || empty($this->record[0]);
    }

    public function raw($query)
    {
        $link = static::getConnection();
        $result = mysqli_query($link, $query);
        if (is_object($result)) {
            //Process result
            $this->record = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $model = new static();
                $model->setProperties($row);
                $model->setFinal();
                $this->record[] = $model;
            }
        } else {
            $this->record = [null];
        }

        return $this;
    }

    private function load($id = null)
    {
        if ($id) {
            $this->primaryValues = $id;
        }

        if (!empty($this->primaryValues)) {
            $this->setPrimaryWhereCondition();
            $query = 'select * from ' . $this->getTable()
                    . ' where ' . $this->getWhereQuery();
            $this->raw($query);

            if (!$this->isEmpty()) {
                $this->setProperties();
                $this->setFinal();

                return $this;
            }
        }
    }

    public static function find($id)
    {
        $model = new static($id);

        return $model;
    }

    private function setFinal()
    {
        if (is_array($this->primary)) {
            $this->primaryValues = [];
            foreach ($this->primary as $compositeKey) {
                $this->primaryValues[] = isset($this->{$compositeKey}) ? $this->{$compositeKey} : '';
            }
        } else {
            $this->primaryValues = isset($this->{$this->primary}) ? $this->{$this->primary} : '';
        }
        $this->setPrimaryWhereCondition();
        $this->final = true;
    }

    public static function instance()
    {
        return new static();
    }

    /**
     * Fetch results as array if it exists, else make a new query.
     */
    public function fetch($limit = null, $offset = 0)
    {
        if ($this->isEmpty()) {
            $query = 'select * from ' . $this->getTable();

            $where = $this->getWhereQuery();
            if (!empty($where)) {
                $query .= ' where ' . $where;
            }

            if (!empty($limit)) {
                $query .= " limit $limit";
            }

            if (!empty($offset)) {
                $query .= " offset $offset";
            }

            return $this->raw($query)->record;
        } else {
            if ($limit || $offset) {
                return array_slice($this->all(), $offset, $limit);
            } else {
                return $this->all();
            }
        }
    }

    /**
     * Return result as array.
     */
    public function all()
    {
        return (array) $this->record;
    }

    /**
     * Return first result. similar to fetch with a limit of 1.
     * @return Model First model
     */
    public function first()
    {
        if (is_array($this->record) && isset($this->record[0])) {
            //Return first value or an empty model
            return $this->record[0] ?: new static();
        } else {
            $result = $this->fetch(1);

            return isset($result[0]) ? $result[0] : new static();
        }
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public static function create(array $data)
    {
        $model = new static();
        $link = static::getConnection();
        $query = 'insert into ' . $model->getTable() . ' set ';
        foreach ($data as $key => $value) {
            $query .= "$key = '" . mysqli_escape_string($link, $value) . "',";
        }
        $query = rtrim($query, ',');
        if (mysqli_query($link, $query)) {
            $id = mysqli_insert_id($link);
            $model->load($id);
            if (!$model->isEmpty()) {
                return $model;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Delete this document from collection.
     *
     * @param array $data Conditions to be deleted
     *
     * @return int count of documents deleted
     */
    public function delete()
    {
        $query = 'delete from ' . $this->getTable();
        $where = $this->getWhereQuery();
        if (!empty($where)) {
            $query .= ' where ' . $where;
        }

        return $this->raw($query);
    }

    /**
     * Update a collection.
     *
     * @param string $id   The id of the data to be updated
     * @param array  $data The data to be updated
     *
     * @return int count of documents updated
     */
    public function update(array $data)
    {
        $query = 'update ' . $this->getTable() . ' set ';
        $link = static::getConnection();
        foreach ($data as $key => $value) {
            $query .= $key . '=\'' . mysqli_escape_string($link, $value) . '\',';
        }
        $query = rtrim($query, ',');

        $where = $this->getWhereQuery();
        if (!empty($where)) {
            $query .= ' where ' . $where;
        }

        return $this->raw($query);
    }

    private function setPrimaryWhereCondition()
    {
        if (is_array($this->primary) && is_array($this->primaryValues)) {
            $combine = array_combine($this->primary, $this->primaryValues);
            if ($combine) {
                $this->where($combine);
            } else {
                throw new Exception('Primary keys and values are not of equal length');
            }
        } elseif (!empty($this->primaryValues)) {
            if (is_string($this->primary) && !is_array($this->primaryValues)) {
                $this->where($this->primary, $this->primaryValues);
            } else {
                throw new Exception('Primary key or value type is invalid');
            }
        }
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($this->final) {
            throw new Exception('Cannot modify a final object');
        }

        //If it's a key-value pair array
        if (is_array($column)) {
            $i = 0;
            foreach ($column as $key => $value) {
                $this->wheres[] = [
                    '__column' => $key,
                    '__boolean' => $i === 0 ? $boolean : 'and',
                    '__value' => $value,
                    '__op' => '=',
                ];
                ++$i;
            }

            return $this;
        }

        if ($column instanceof Closure) {
            $where = new static();
            $column($where);
            $this->wheres[] = [
                '__group' => 1,
                '__boolean' => $boolean,
                '__where' => $where->wheres,
            ];

            return $this;
        }

        if ($value instanceof Closure) {
            //Not implemented yet
        }

        //If only 2 parameters are passed, we will assume that the second is the
        //value and the operator is equal (=)
        if (func_num_args() === 2 || empty($value)) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            '__column' => $column,
            '__boolean' => $boolean,
            '__value' => $value,
            '__op' => $operator,
        ];

        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    private function getWhereQuery(array $conditions = null)
    {
        if (empty($conditions)) {
            $conditions = $this->wheres;
        }

        $query = '';
        $i = 0;
        foreach ($conditions as $key => $value) {
            //Set boolean. Of course not in front of the first column in the set
            if ($i !== 0) {
                if (isset($value['__boolean'])) {
                    $query .= " {$value['__boolean']} ";
                } else {
                    $query .= ' and ';
                }
            }

            //If __group key is set then the programmer wants to group this set
            //of conditions in __where
            if (is_array($value) && isset($value['__group'])) {
                $query .= '(' . $this->getWhereQuery($value['__where']) . ')';
            } elseif (is_array($value)) {
                if (isset($value['__op'])) {
                    //Set column, operation and value
                    $query .= "{$value['__column']}{$value['__op']}'{$value['__value']}'";
                } else {
                    //If no operation is set then we assume an equals to operation
                    $query .= "{$value['__column']}='{$value['__value']}'";
                }
            } else {
                //If value is not an array we assume key=value
                $query .= "$key='$value'";
            }

            ++$i;
        }

        return $query;
    }

    public function save()
    {
        $properties = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);
        //Check properties and create
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->record)) {
            return $this->record[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this->record[$name] = $value;
    }

    public function paginate($limit = 10, $page = null)
    {
        if (!$page) {
            $page = 1;
        }
        return $this->fetch($limit, (($page - 1) * 10));
    }
}
