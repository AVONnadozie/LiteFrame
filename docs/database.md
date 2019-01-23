# Database
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

## Model Events
> Explain model events

## setProperty* and getProperty* functions
> Explain the setProperty* and getProperty* functions

Find more on [RedBeanPHP website](https://redbeanphp.com/crud)