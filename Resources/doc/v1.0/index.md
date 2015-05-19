# How to use the TableBundle
Welcome to TableBundle - a bundle to create your tables in an easy way.

## Intro
* [Why should I use this bundle?](index.md#why)
* [How to insall](index.md#install)

## Tutorials
* [Create a simple table](doc/tutorial/simple_table.md)
* Use pagination in your table (TODO)
* Sort your table (TODO)
* Add filters to your table (TODO)
* Custom table rendering (TODO)

## Documenation
* Twig-Functions
* Columns 
* Content grabber
* Filter

## API
* Add your own Column
* Add your own Filter (TODO)
* Add your own DataSource (TODO)

<a name="why"></a>
## Why should I use this bundle?
Creating tables for data, stored in your database or file system, is a cumbersome procedure.
You have to do this many times, sometimes you may use the same table in different views.
Additional, you have to keep your horrible html sources in good condition.
Here is why you should use this bundle:
* Create your tables in a small file, using a php class
* Use instances of that table as often as you want
* Keep only one file in good condition
* Don't worry about pagination, sortable fields or search for content, the table bundle will do this for you

<a name="install"></a>
## Installation
### Get the bundle
There are different methods to get the bundle:
#### Method 1) Via composer
Add the bundle to your `require` section of your `composer.json`.
```
"require" :  {
    // ...
    "jgm/table-bundle": "~1.0"
}
```
#### Method 2) Download from github
Donwload the bundle from `https://github.com/jangemue/TableBundle.git` and add the unpacked folder to your `src` directory.
### Add the bundle to your app
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ... other bundles ...
        new JGM\TableBundle\TableBundle(),
    );
    // ...
}
```
