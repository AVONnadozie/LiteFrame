<?php

/**
 * Returns an array containing only the allowed fields.
 *
 * @param mixed $object
 * @param array $allowed_fields An array of allowed fields.<br/>
 *                              For deeper matching, replace the field with another array. e.g
 *                              <code>'field' => ['inner_field1','inner_field2']</code>.<br/><br/>
 *                              To select all rows in a field, use '*' as key for the fields to select. e.g
 *                              <code>'field' => ['*' => ['inner_field1','inner_field2']]</code><br/>
 *
 * Example<br/>
 * <code>
 * $john = \App\Models\User::find(1);
 * $filtered = filter_collection($john, ['uploadedEntries' => [
 *           '*' => ['slug', 'title', 'views' => ['*' => ['viewed_at']]]
 *          ]
 *     ]);
 * </code><br/>
 * This will select all rows in uploadedEntries with fields slug, title and views
 * and then all rows in views with field viewed_at. It can go deeper!
 *
 * @return array
 */
function filterFields($object, array $allowed_fields)
{
    $data = [];
    foreach ($allowed_fields as $key => $field) {
        if (!is_array($field)) {
            //For one dimensional array, we simply fetch the data whose key
            //is allowed. No stress!
            try {
                $data[$field] = $object[$field];
            } catch (\Exception $e) {
                //Key doesn't exist, ignore
            }
        } else {
            if ($key === '*') {
                //We recursively filter data in all rows
                foreach ($object as $iKey => $item) {
                    $data[$iKey] = filterFields($item, $field);
                }
            } else {
                //We recursively filter the specified row
                $data[$key] = filterFields($object[$key], $field);
            }
        }
    }

    return $data;
}

function paginate($collection, $perPage = 10)
{
}
