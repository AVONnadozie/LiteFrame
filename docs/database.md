# Database

## Finding Records
Finding stuff in the database is easy:
```php
<?php

$post = SampleModel::withId(3);
```
This will get the post with the id of 3
```php
<?php

$posts = SampleModel::find('title LIKE ?', [ 'holiday' ] );
```
This will search for all posts having the word 'holiday' in the title and will return an collection containing all the relevant beans as a result. As you see, we don't use a fancy query builder, just good old SQL.
We like to keep things simple.

Besides using the `find()` functions, you can also use **raw** SQL queries:
```php
<?php

$posts = DB::getAll('SELECT * FROM posts WHERE comments < ? ', [ 50 ] );
```

## Model vs OODBean
An OODBean or bean is an object mapping of a record in your database.
Models internally wraps a bean, and provides more functions that makes it easier to manipulate bean objects.
You can call `Model->getBean()` to get the underlying bean

##### Code Difference

Using Model (The Liteframe Way)
```php
<?php

$post = SampleModel::dispense();
$post->title = 'My holiday';
$id = $post->save();
```

Using RedBeanPHP's R (The RedBeanPHP Way)
```php
<?php

$post = R::dispense('posts');
$post->title = 'My holiday';
R::store($post);
```

Both examples above creates a new bean, sets it's title to 'My holiday' and saves it to the database.
Whichever method you prefer to use is fine.

## DB vs R
`R` is the standard class by ReadBeanPHP for manipulating beans
`DB` (recommended) is an `R` with more functions

## Relationships
RedBeanPHP also makes it easy to manage relations. For instance, if we like to add some photos to our holiday post we do this:
```php
<?php

$post->ownPhotoList[] = $photo1;
$post->ownPhotoList[] = $photo2;
$post->save();
```
Here, `$photo1` and `$photo2` are also beans (but of type 'photo'). 
After storing the post, these photos will be associated with the blog post. 
To associate a bean you simply add it to a list. The name of the list must match the name of the related bean type.

So photo beans go in:
`$post->ownPhotoList`

comments go in: 
`$post->ownCommentList` 

and notes go in: 
`$post->ownNoteList` 

See? It's that simple!

To retrieve associated beans, just access the corresponding list:
```php
<?php

$post = SampleModel::load($id );
$firstPhoto = reset( $post->ownPhotoList );
```

In the example above, we load the blog post and then access the list. 
The moment we access the `ownPhotoList` property, the relation will be loaded automatically, 
this is often called lazy loading, because RedBeanPHP only loads the beans when you really need them.

To get the first element of the photo list, we simply used PHP's native `reset()` function

> For more information, see RedBeanPHP's documentation on  [Relationships](https://redbeanphp.com/index.php?p=/one_to_many)

## Model Events
> Explain model events

## setProperty* and getProperty* functions
> Explain the setProperty* and getProperty* functions

Find more on [RedBeanPHP website](https://redbeanphp.com/crud)