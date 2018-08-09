<?php

namespace LiteFrame\Database;

use LiteFrame\Utility\Collection;
use LiteFrame\Utility\Inflector;
use RedBeanPHP\Finder;
use RedBeanPHP\OODBBean;

class Model extends OODBBean
{
    protected $table;
    protected $updateTimestamps = true;
    protected $fillable = [];

    public function __construct(OODBBean $bean = null)
    {
        if ($bean) {
            cast($this, $bean);
        }
    }
    
    public static function getTable()
    {
        $model = new static;
        if (empty($model->table)) {
            $classname = get_called_class();
            $split = explode('\\', $classname);
            $name = array_pop($split);
            //Generate table name
            //static::$table = Inflector::tableize($name);
            $model->table = Inflector::pluralize(strtolower($name));
        }
        return $model->table;
    }
    
    /**
     * Converts OODBBean to Model
     * @param OODBBean|array $bean
     * @return \static|Collection
     */
    public static function createFromBean($bean)
    {
        if (is_array($bean)) {
            $collection = new Collection($bean);
            $collection->map(function ($item) {
                return static::createFromBean($item);
            });
            return $collection;
        }
        
        if ($bean instanceof OODBBean) {
            //$cast = new static;
            //return cast($cast, $bean);
            return new static($bean);
        } else {
            return $bean;
        }
    }
    
    /**
     * Dispenses this bean
     * @return $this
     */
    public static function instance($num = 1, $alwaysReturnArray = false)
    {
        $bean = DB::dispense(static::getTable(), $num, $alwaysReturnArray);
        return static::createFromBean($bean);
    }
    
    /**
     * Returns the model with the id.
     *
     * @param int $id Item id
     *
     * @return Model|NULL
     */
    public static function withId($id)
    {
        return static::findOne('id = ?', [$id]);
    }

    /**
     * Finds a bean using this model and a where clause (SQL).
     * As with most Query tools in RedBean you can provide values to
     * be inserted in the SQL statement by populating the value
     * array parameter; you can either use the question mark notation
     * or the slot-notation (:keyname).
     *
     * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings array of values to be bound to parameters in query
     *
     * @return Collection
     */
    public static function find($sql = null, $bindings = [])
    {
        $items = DB::find(static::getTable(), $sql, $bindings);
        return static::createFromBean($items);
    }

    /**
     * The findAll() method differs from the find() method in that it does
     * not assume a WHERE-clause, so this is valid:
     *
     * Model::findAll(' ORDER BY name DESC ');
     *
     * Your SQL does not have to start with a valid WHERE-clause condition.
     *
     * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings array of values to be bound to parameters in query
     *
     * @return Collection
     */
    public static function findAll($sql = null, $bindings = [])
    {
        $items = DB::findAll(static::getTable(), $sql, $bindings);
        return static::createFromBean($items);
    }
    
    
    /**
     * Like find() but also exports the beans as an array.
     * This method will perform a find-operation. For every bean
     * in the result collection this method will call the export() method.
     * This method returns an array containing the array representations
     * of every bean in the result set.
     *
     * @see Finder::find
     *
     * @param string $sql      sql    SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings values array of values to be bound to parameters in query
     *
     * @return Collection
     */
    public static function findAndExport($sql = null, $bindings = [])
    {
        $items = DB::findAndExport(static::getTable(), $sql, $bindings);
        return static::createFromBean($items);
    }
    
    
    /**
     * Tries to find beans matching the specified type and
     * criteria set.
     *
     * If the optional additional SQL snippet is a condition, it will
     * be glued to the rest of the query using the AND operator.
     *
     * @param array  $like optional criteria set describing the bean to search for
     * @param string $sql  optional additional SQL for sorting
     *
     * @return Collection
     */
    
    public static function findLike($like = [], $sql = '')
    {
        $items =  DB::findLike(static::getTable(), $like, $sql);
        return static::createFromBean($items);
    }
    
    /**
     * Like Model::find() but returns the first bean only.
     *
     * @param string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param array  $bindings array of values to be bound to parameters in query
     *
     * @return Model|NULL
     */
    
    public static function findOne($sql = null, $bindings = [])
    {
        $bean =  DB::findOne(static::getTable(), $sql, $bindings);
        return static::createFromBean($bean);
    }
    
    
    /**
     * Finds a BeanCollection using the repository.
     * A bean collection can be used to retrieve one bean at a time using
     * cursors - this is useful for processing large datasets. A bean collection
     * will not load all beans into memory all at once, just one at a time.
     *
     * @param  string $sql      SQL query to find the desired bean, starting right after WHERE clause
     * @param  array  $bindings values array of values to be bound to parameters in query
     *
     * @return BeanCollection
     */
    
    public static function findCollection($sql = null, $bindings = [])
    {
        $collection = DB::findCollection(static::getTable(), $sql, $bindings);
        return new BeanCollection($collection);
    }
    
    /**
     * Returns a collection of beans. Pass a series of ids and
     * this method will bring you the corresponding beans.
     *
     * important note: Because this method loads beans using the load()
     * function (but faster) it will return empty beans with ID 0 for
     * every bean that could not be located. The resulting beans will have the
     * passed IDs as their keys.
     *
     * @param array  $ids  ids to load
     *
     * @return Collection
     */
    
    public static function batch(array $ids)
    {
        $items = DB::batch(static::getTable(), $ids);
        return static::createFromBean($items);
    }
    
    /**
     * Loads a bean from the object database.
     * It searches for this bean Object in the
     * database. It does not matter how this bean has been stored.
     * RedBean uses the primary key ID $id and the string $type
     * to find the bean. The $type specifies what kind of bean you
     * are looking for; this is the same type as used with the
     * instance() function. If RedBean finds the bean it will return
     * the Bean object; if it cannot find the bean
     * RedBean will return a new bean with
     * primary key ID 0. In the latter case it acts basically the
     * same as instance().
     *
     * Important note:
     * If the bean cannot be found in the database, a new bean of
     * this type will be generated and returned.
     *
     * Usage:
     *
     * <code>
     * $post = SampleModel::instance();
     * $post->title = 'my post';
     * $id = $post->save();
     * $post = SampleModel::load( $id );
     * $post->delete( $post );
     * </code>
     *
     * In the example above, we create a new bean of SampleModel.
     * We then set the title of the bean to 'my post' and we
     * store the bean. The save() method will return the primary
     * key ID $id assigned by the database. We can now use this
     * ID to load the bean from the database again and delete it.
     *
     * @param integer $id      ID of the bean you want to load
     * @param string  $snippet string to use after select  (optional)
     *
     * @return Model
     */
    public static function load($id, $snippet = null)
    {
        $bean = DB::load(static::getTable(), $id, $snippet);
        return static::createFromBean($bean);
    }
    
    /**
     * Alias for batch().
     *
     * @param array  $ids  ids to load
     *
     * @return array
     */
    public function loadAll($ids)
    {
        $items = DB::loadAll($this->getTable(), $ids);
        return static::createFromBean($items);
    }
    
    /**
     * Same as load, but selects the bean for update, thus locking the bean.
     * This equals an SQL query like 'SELECT ... FROM ... FOR UPDATE'.
     * Use this method if you want to load a bean you intend to UPDATE.
     * This method should be used to 'LOCK a bean'.
     *
     * Usage:
     *
     * <code>
     * $bean = Model::loadForUpdate( $id );
     * ...update...
     * $bean->save();
     * </code>
     *
     * @param integer $id  ID of the bean you want to load
     *
     * @return Model
     */
    public function loadForUpdate($id)
    {
        $bean = DB::loadForUpdate($this->getTable(), $id);
        return static::createFromBean($bean);
    }
    
    
    
    /**
     * MatchUp is a powerful productivity boosting method that can replace simple control
     * scripts with a single RedBeanPHP command. Typically, matchUp() is used to
     * replace login scripts, token generation scripts and password reset scripts.
     * The MatchUp method takes an SQL query snippet (starting at the WHERE clause),
     * SQL bindings, a pair of task arrays and a bean reference.
     *
     * If the first 3 parameters match a bean, the first task list will be considered,
     * otherwise the second one will be considered. On consideration, each task list,
     * an array of keys and values will be executed. Every key in the task list should
     * correspond to a bean property while every value can either be an expression to
     * be evaluated or a closure (PHP 5.3+). After applying the task list to the bean
     * it will be stored. If no bean has been found, a new bean will be dispensed.
     *
     * This method will return TRUE if the bean was found and FALSE if not AND
     * there was a NOT-FOUND task list. If no bean was found AND there was also
     * no second task list, NULL will be returned.
     *
     * To obtain the bean, pass a variable as the fifth parameter.
     * The function will put the matching bean in the specified variable.
     *
     * @param string   $sql          SQL snippet (starting at the WHERE clause, omit WHERE-keyword)
     * @param array    $bindings     array of parameter bindings for SQL snippet
     * @param array    $onFoundDo    task list to be considered on finding the bean
     * @param array    $onNotFoundDo task list to be considered on NOT finding the bean
     * @param Model &$model        reference to obtain the found bean
     *
     * @return mixed
     */
    public static function matchUp($sql, $bindings = array(), $onFoundDo = null, $onNotFoundDo = null, &$model = null)
    {
        $result = DB::matchUp($this->getTable(), $sql, $bindings, $onFoundDo, $onNotFoundDo, $bean);
        if ($model) {
            $model = static::createFromBean($bean);
        }
        return $result;
    }
    
    /**
     * Stores a bean in the database. If the database schema is not compatible
     * with this bean and RedBean runs in fluid mode the schema
     * will be altered to store the bean correctly.
     * If the database schema is not compatible with this bean and
     * RedBean runs in frozen mode it will throw an exception.
     * This function returns the primary key ID of the inserted
     * bean.
     *
     * The return value is an integer if possible. If it is not possible to
     * represent the value as an integer a string will be returned.
     *
     * Usage:
     *
     * <code>
     * $post = SampleModel::instance();
     * $post->title = 'my post';
     * $id = $post->save();
     * $post = SampleModel::load( $id );
     * $post->delete( $post );
     * </code>
     *
     * In the example above, we create a new bean of SampleModel.
     * We then set the title of the bean to 'my post' and we
     * store the bean. The save() method will return the primary
     * key ID $id assigned by the database. We can now use this
     * ID to load the bean from the database again and delete it.
     *
     *
     * @return integer|string
     */
    public function save()
    {
        if ($this->updateTimestamps) {
            if (!$this->id && !$this->created_at) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            $this->updated_at = date('Y-m-d H:i:s');
        }
        return DB::store($this);
    }
    
    /**
     * Removes this bean from the database.
     *
     * Usage:
     *
     * <code>
     * $post = SampleModel::instance();
     * $post->title = 'my post';
     * $id = $post->save();
     * $post = SampleModel::load( $id );
     * $post->delete( $post );
     * </code>
     *
     * In the example above, we create a new bean of SampleModel.
     * We then set the title of the bean to 'my post' and we
     * store the bean. The save() method will return the primary
     * key ID $id assigned by the database. We can now use this
     * ID to load the bean from the database again and delete it.
     *
     * @return void
     */
    public function delete()
    {
        return DB::trash($this);
    }
    
    
    /**
     * Calculates a diff between this bean and another bean (or arrays of beans).
     * The result of this method is an array describing the differences of the second bean compared to
     * this bean, where this bean is taken as reference. The array is keyed by type/property, id and property name, where
     * type/property is either the type (in case of the root bean) or the property of the parent bean where the type resides.
     * The diffs are mainly intended for logging, you cannot apply these diffs as patches to other beans.
     * However this functionality might be added in the future.
     *
     * The keys of the array can be formatted using the $format parameter.
     * A key will be composed of a path (1st), id (2nd) and property (3rd).
     * Using printf-style notation you can determine the exact format of the key.
     * The default format will look like:
     *
     * 'book.1.title' => array( <OLDVALUE>, <NEWVALUE> )
     *
     * If you only want a simple diff of one bean and you don't care about ids,
     * you might pass a format like: '%1$s.%3$s' which gives:
     *
     * 'book.1.title' => array( <OLDVALUE>, <NEWVALUE> )
     *
     * The filter parameter can be used to set filters, it should be an array
     * of property names that have to be skipped. By default this array is filled with
     * two strings: 'created' and 'modified'.
     *
     * @param OODBBean|array $other   beans to compare
     * @param array          $filters names of properties of all beans to skip
     * @param string         $format  the format of the key, defaults to '%s.%s.%s'
     * @param string         $type    type/property of bean to use for key generation
     *
     * @return array
     */
    public function diff($other, $filters = array( 'created', 'modified' ), $pattern = '%s.%s.%s')
    {
        return DB::diff($this, $other, $filters, $pattern);
    }
    
    /**
     * Short hand function to find and trash beans.
     * This function combines trashAll and find.
     * Given a query snippet and optionally some parameter
     * bindings, this function will search for the beans described in the
     * query and its parameters and then feed them to the trashAll function
     * to be trashed.
     *
     * Note that while this function accepts just
     * a bean type and query snippet, the beans will still be loaded first. This is because
     * the function still respects all the FUSE hooks that may have beeb
     * associated with the domain logic associated with these beans.
     * If you really want to delete just records from the database use
     * a simple DELETE-FROM SQL query instead.
     *
     * @param string $sqlSnippet an SQL query snippet
     * @param array  $bindings   SQL parameter bindings
     *
     * @return array
     */
    public function hunt($sqlSnippet, $bindings = [])
    {
        return DB::hunt($this->getTable(), $sqlSnippet, $bindings);
    }

    /**
     * Counts the number of beans of this model.
     * This method accepts a first argument to modify the count-query.
     * A second argument can be used to provide bindings for the SQL snippet.
     *
     * @param string $addSQL   additional SQL snippet
     * @param array  $bindings parameters to bind to SQL
     *
     * @return integer
     */
    public static function countAll($addSQL = '', $bindings = array())
    {
        return DB::count(static::getTable(), $addSQL, $bindings);
    }

    /**
     * Auto fill bean columns.<br/>
     * Note that only column names specified as fillable will be filled.
     * @param array $data column-value pair to fill
     */
    public function fill(array $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->{$key} = $value;
            }
        }
    }


    /**
     * Chainable method to cast a certain ID to a model; for instance:
     * $person = $club->fetchAs(Person::class)->member;
     * This will load a bean of model Person using member_id as ID.
     *
     * @return OODBBean
     */
    public function fetchAs($model) {
        $bean = new $model;
        return parent::fetchAs($bean->getTable());
    }

    /**
     * Create one-to-many relationship
     * @param \LiteFrame\Database\Model $related
     */
    public function owns(Model $related)
    {
        $table = ucfirst($related->getTable());
        $column = "own{$table}List";
        $this->$column[] = $related;
    }

    /**
     * Create many-to-many relationship
     * @param \LiteFrame\Database\Model $related
     */
    public function hasMany(Model $related)
    {
        $table = ucfirst($related->getTable());
        $column = "shared{$table}List";
        $this->$column[] = $related;
    }

    /**
     * Create reverse one-to-many relationship
     * @param \LiteFrame\Database\Model $related
     * @param type $foreignKey
     */
    public function belongsTo(Model $related, $foreignKey = '')
    {
        if (!$foreignKey) {
            $table = $related->getTable();
            $column = Inflector::singularize($table);
        } else {
            $column = $foreignKey;
        }
        $this->$column = $related;
    }
}
