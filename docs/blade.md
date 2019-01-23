# Templating
By standard, the original Blade library is part of Laravel (Illuminate components) and 
to use this template library, you're required to install Laravel and Illuminate-view components.

With BladeOne, we're able to import most features of Laravel blade without having to 
install unwanted Illuminate components.

They are no significant difference in syntax or usage between this template engine and that of the existing Laravel blade, 
therefore, if you're already familiar with Laravel blade templating
you might find this documentation pretty boring and hence advised to skip.

## Usage

### Inheritance

#### In parent view *(layout page)*
|Tag|Note|
|---|---|
|`@section('sidebar')`|Start a new section with name as `sidebar`|
|`@show`|Indicates where the content of section will be displayed|
|`@yield('title')`|Shows here the content of the section named `title`|

#### In child view *(using the layout page)*
|Tag|Note|
|---|---|
|`@extends('layouts.page')`|Instructs the application to inherit a parent view with name `layouts.page` |
|`@section('title', 'My Title')`|Sets 'My Title' as content of the section `title`|
|`@section('sidebar')`|Starts a block of code as content of a section named `sidebar`|
|`@endsection`|End a block of code|


**Example**

Parent View
```html
<html>
    <head>
        <title>@yield('title')</title>
    </head>
    <body>
        <div>Content</div>
        <div>@section('sidebar')</div>
    </body>
</html>
```

Child View
```blade
@section('title', 'Welcome to ' . $app_name)
@section('sidebar')
    This is a sidebar for {{$app_name}}
@endsection
```

Given that a variable `$app_name` = `'Blade'`, the resulting HTML will be
```html
<html>
    <head>
        <title>Welcome to Blade</title>
    </head>
    <body>
        <div>Content</div>
        <div>This is a sidebar for Blade</div>
    </body>
</html>
```


### Variables
|Tag|Note|
|---|---|
|`{{$variable}}`|escapes and displays the value of the variable using `htmlentities` to avoid xss attacks|
|`@{{$variable}}`|show the value of the content directly (not evaluated, useful for js)|
|`{!!$variable!!}`|Displays the value of the variable without escaping|
|`{{ $variable or 'Default' }}`|Displays the variable or default if the variable is null or undefined|
|`{{Class::StaticFunction($variable)}}`|calls and displays the value of a function|

### Logic
|Tag|Note|
|---|---|
|`@if(boolean)`|if logic-conditional statement|
|`@elseif(boolean)`|else if logic-conditional statement|
|`@else`|else logic statement|
|`@endif`|end if logic statement|
|`@unless(boolean)`|execute block of code if boolean is false|

### Loop

#### For loop
Template
```blade
@for($variable; $condition; $increment) 
    //List items here
@endfor
```

_Generates a loop until the condition is met and the variable is incremented for each loop_   

|Tag|Note|Example|
|---|---|---|
|`$variable`|is a variable that should be initialized.|$i=0|  
|`$condition`|is the condition that must be true, otherwise the cycle will end.|$i<10|
|`$increment`|is how the variable is incremented in each loop.|$i++|

Example:   
```blade
@for ($i = 0; $i < 3; $i++)
    The current value is {{ $i }}<br>
@endfor
```
Returns:   
```html
The current value is 0
The current value is 1
The current value is 2
```

#### Foreach
Template
```blade
@foreach($array as $alias) / @endforeach
```
Generates a loop for each values of the variable.    

|Tag|Note|Example|
|---|---|---|
|`$array`|Array to loop through|$countries|  
|`$alias`|A variable that holds the item in the current loop.|$country|

Example: Given that `$users` is an array of objects
```blade
@foreach($users as $user)
    This is user {{ $user->id }}
@endforeach
```
Returns:
```html
This is user 1
This is user 2
```

#### Forelse
Template
```blade
@forelse($array as $alias) 
    //List item here
@empty 
    //Default, goes here. Something to display if the array is empty
@endforelse

```
Its the same as foreach but jumps to the `@empty` tag if the array is null or empty   

|Tag|Note|Example|
|---|---|---|
|`$array`|Array to loop through|$countries|  
|`$alias`|A variable that holds the item in the current loop.|$country|


Example: Given that $users is an array of objects.
```blade
@forelse($users as $user)
    <li>{{ $user->name }}</li>
@empty
    <p>No users</p>
@endforelse
```
Returns:
```html
John Doe
Anna Smith
```

#### While
Template
```blade
@while($condition) / @endwhile
```
Loops until the condition is not meet.

|Tag|Note|Example|
|---|---|---|
|`$condition`|The cycle loops until the condition is false.|$counter<10|  


Example: ($users is an array of objects)
```html
@set($whilecounter=0)
@while($whilecounter<3)
    @set($whilecounter)
    I'm looping forever.<br>
@endwhile
```
Returns:
```html
I'm looping forever.
I'm looping forever.
I'm looping forever.
```

#### @splitforeach($nElem,$textbetween,$textend="")  inside @foreach
This functions show a text inside a `@foreach` cycle every "n" of elements.  This function could be used when you want to add columns to a list of elements.   
NOTE: The `$textbetween` is not displayed if its the last element of the last.  With the last element, it shows the variable `$textend`

|Tag|Note|Example|
|---|---|---|
|$nElem|Number of elements|2, for every 2 element the text is displayed|  
|$textbetween|Text to show|`</tr><tr>`| 
|$textend|Text to show|`</tr>`| 

Example: ($users is an array of objects)
```html
<table border="1">
<tr>
@foreach($drinks7 as $drink)
    <td>{{$drink}}</td>
    @splitforeach(2,'</tr><tr>','</tr>')
    @endforeach
</table>
```
Returns a table with 2 columns.

#### @continue / @break
Continue jump to the next iteration of a cycle.  `@break` jump out of a cycle.

|Tag|Note|Example|
|---|---|---|

Example: ($users is an array of objects)
```html
@foreach($users as $user)
    @if($user->type == 1) // ignores the first user John Smith
    @continue
    @endif
    <li>{{ $user->type }} - {{ $user->name }}</li>

    @if($user->number == 5) // ends the cycle.
        @break
    @endif
@endforeach
```
Returns:
```html
2 - Anna Smith
```
### switch / case

_Example:(the indentation is not required)_
```html
@switch($countrySelected)
    @case(1)
        first country selected<br>
    @break
    @case(2)
        second country selected<br>
    @break
    @defaultcase()
        other country selected<br>
@endswitch()
```

- `@switch` The first value is the variable to evaluate.
- `@case` Indicates the value to compare.  It should be run inside a @switch/@endswitch
- `@default` (optional) If not case is the correct then the block of @defaultcase is evaluated.
- `@break` Break the case
- `@endswitch` End the switch.

### Sub Views
|Tag|Note|
|---|---|
|@include('folder.template')|Include a template|
|@include('folder.template',['some' => 'data'])|Include a template with new variables|
|@each('view.name', $array, 'variable')|Includes a template for each element of the array|
Note: Templates called folder.template is equals to folder/template

## @include
It includes a template

You could include a template as follow:
```html
<div>
    @include('shared.errors')
    <form>
        <!-- Form Contents -->
    </form>
</div>
```

You could also pass parameters to the template
```html
@include('view.name', ['some' => 'data'])
```
### @includeif

Additionally, if the template doesn't exist then it will fail. You could avoid it by using includeif
```html
@includeIf('view.name', ['some' => 'data'])
```
### @includefast

`@Includefast` is similar to `@include`. However, it doesn't allow parameters because it merges the template in a big file (instead of relying on different files), so it must be fast at runtime by using more space on the hard disk versus less call to read a file.


```html
@includefast('view.name')
```

>This template runs at compile time, so it doesn't work with runtime features such as @if() @includefast() @endif()

## Comments
|Tag|Note|
|---|---|
|{{-- text --}}|Include a comment|

### Stacks
|Tag|Note|
|---|---|
|@push('elem')|Add the next block to the push stack|
|@endpush|End the push block|
|@stack('elem')|Show the stack|

## @set (new for 1.5)
```
@set($variable=[value])
```
`@set($variable)` is equals to `@set($variable=$variable+1)`
- `$variable` defines the variable to add. If not value is defined and it adds +1 to a variable.
- value (option) define the value to use.

### Service Inject
|Tag|Note|
|---|---|
|@inject('metrics', 'App\Services\MetricsService')|Used for insert a Laravel Service|NOT SUPPORTED|

## Asset Management

The next libraries are designed to work with assets (CSS, JavaScript, images and so on). While it's possible to show an asset without a special library but it's a challenge if you want to work with relative path using an MVC route.

For example, let's say the next example:
http://localhost/img/resource.jpg

you could use the full path.
```html
<img src='http://localhost/img/resource.jpg' />
```
However, it will fail if the server changes.
So, you could use a relative path.
```html
<img src='img/resource.jpg' />
```
However, it fails if you are calling the web
http://localhost/controller/action/

because the browser will try to find the image at
http://localhost/controller/action/img/resource.jpg
instead of
http://localhost/img/resource.jpg

So, the solution is to set a base URL and to use an absolute or relative path

Absolute using `@asset`
```html
<img src='@asset("img/resource.jpg")' />
```
is converted to
```html
<img src='http://localhost/img/resource.jpg' />
```

Relative using @relative
```html
<img src='@relative("img/resource.jpg")' />
```
is converted to (it depends on the current url)
```html
<img src='../../img/resource.jpg' />
```

It is even possible to add an alias to resources. It is useful for switching from local to CDN.

```php
$blade->addAssetDict('js/jquery.min.js','https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');
```
so then
```html
@asset('js/jquery.min.js')
```

returns
```html
https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js
```

:file_folder: Example: [BladeOne/examples/relative1/relative2/callrelative.php](https://github.com/EFTEC/BladeOne/blob/master/examples/examplerelative.php)


### @asset
It returns an absolute path of the resource. 

```html
@asset('js/jquery.js')
```
Note: it requires to set the base address as 
```php
$obj=new BladeOne();
$obj->setBaseUrl("https://www.example.com/urlbase/"); // with or without trail slash
```
> Security: Don't use the variables $SERVER['HTTP_HOST'] or $SERVER['SERVER_NAME'] unless the url is protected or the address is sanitized.

### @resource

It's similar to `@asset`. However, it uses a relative path.
```
@resource('js/jquery.js')
```


Note: it requires to set the base address as 
```php
$obj=new BladeOne();
$obj->setBaseUrl("https://www.example.com/urlbase/"); // with or without trail slash
```

### setBaseUrl($url)
It sets the base url.

```php
$obj=new BladeOne();
$obj->setBaseUrl("https://www.example.com/urlbase/"); // with or without trail slash
```


### getBaseUrl()
It gets the current base url.

```php
$obj=new BladeOne();
$url=$obj->getBaseUrl(); 
```

### addAssetDict($name,$url)
It adds an alias to an asset. It is used for `@asset` and `@relative`. If the name exists then `$url` is used.

```php
$obj=new BladeOne();
$url=$obj->addAssetDict('css/style.css','http://....'); 
```

