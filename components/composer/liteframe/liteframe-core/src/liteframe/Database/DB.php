<?php
namespace LiteFrame\Database;

class DB extends \R
{
    /**
     * Makes a deep copy of a bean. This method makes a deep copy
     * of the bean.The copy will have the following:
     *
     * * All beans in own-lists will be duplicated as well
     * * All references to shared beans will be copied but not the shared beans themselves
     * * All references to parent objects (_id fields) will be copied but not the parents themselves
     *
     * In most cases this is the desired scenario for copying beans.
     * This function uses a trail-array to prevent infinite recursion, if a recursive bean is found
     * (i.e. one that already has been processed) the ID of the bean will be returned.
     * This should not happen though.
     *
     * Note:
     * This function does a reflectional database query so it may be slow.
     *
     * Note:
     * This is a simplified version of the deprecated R::dup() function.
     *
     * @param OODBBean $bean  bean to be copied
     * @param array    $white white list filter with bean types to duplicate
     *
     * @return array
     */
    public static function duplicate($bean, $filters = array())
    {
        if ($bean instanceof Model) {
            $origBean = $bean->getBean();
        } else {
            $origBean = $bean;
        }

        $dub = parent::duplicate($origBean, $filters);

        if ($bean instanceof Model) {
            $class = get_class($bean);
            return Model::wrap($dub, $class);
        } else {
            return $dub;
        }
    }
}
