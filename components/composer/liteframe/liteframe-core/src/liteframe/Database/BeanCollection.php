<?php
namespace LiteFrame\Database;

use RedBeanPHP\BeanCollection as RCollection;

class BeanCollection extends RCollection
{
    public function __construct(RCollection $collection)
    {
        $this->type = $collection->type;
        $this->cursor = $collection->cursor;
        $this->repository = $collection->repository;
    }

    /**
     * Returns the next bean in the collection.
     * If called the first time, this will return the first bean in the collection.
     * If there are no more beans left in the collection, this method
     * will return NULL.
     *
     * @return Model|NULL
     */
    public function next()
    {
        $row = $this->cursor->getNextItem();
        if ($row) {
            $beans = $this->repository->convertToBeans($this->type, array( $row ));
            $bean = array_shift($beans);
            return Model::wrap($bean);
        }
        return null;
    }
}
