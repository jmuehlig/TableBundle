# Create a simple table
Creating tables with this bundle is very easy.

## Create the type class
First you have to define a type, comparable to the form type definition.
You have to build the table, set the name and data source.
Optional you can define options and attribute setter for rows, depending on the row itself.

Here is a simple table type:
```php
<?php
// src/YourBundle/Table/Type/SimpleTableType.php

use JGM\TableBundle\Table\DataSource\DataSourceInterface;
use JGM\TableBundle\Table\TableBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleTableType extends AbstractType
{
	public function buildTable(TableBuilder $builder)
	{
		$builder
			->add('text', 'id')
			->add('text', 'username', array(
				'attr' => array('width' => '90%')
			));
	}
	
	public function getName()
	{
		return 'simple_table';
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaultOptions(array(
			'attr' => array('class' => 'table'),
			'empty_value' => 'This table is empty :-('
		));
	}
	
	public function getDataSource(ContainerInterface $container)
	{
		return new EntityDataSource('YourBundle:Entity');
	}
}
```

## Add columns
You can add a new columns to your table by calling the method `TableBuilder::add`, using the `buildTable` method.
Syntax of the `add` method is specified as follows: `TableBuilder::add(string $columnType, string $columnName[, array $options])`.
Each column type brings his own column rendering and options.
The pre-defined types are:
* [text](columns/text.md)
* [number](columns/number.md)
* [date](columns/date.md)
* [entity](columns/entity.md)
* [content](columns/content.md)
* [counter](columns/counter.md)

## Type options
You can specify your table with some options:
* *empty_value* (type: `string`, default: *No data found.*): Value that will be shown, if no data is available.
* *attr* (type: `array`, default: `null`): Attributes for the `<table>`-tag.
* *head_attr* (type: `array`, default: `null`): Attributes for the table head.
* *renderer* (type: `RendererInterface`, default: `DefaultRenderer`): Renderer for the table, see the [api](../api/renderer.md) for more information.

## Data source
The table bundle will not restrict you on database only as source for your data.
With the data source feature, you can choose a source and if you have a look into the [api](../api/data_source.md),
you can also implement your own data sources (e.g. file data source, REST data source, ...).
Available sources are the entity data source and for more control the query builder data source.

### EntityDataSource
If you only want all rows of an entity, the entity data source 
`EntityDataSource(string $entity[, callable $callback])`

### QueryBuilderDataSource


## Row attributes
For more control, you can specify the `<tr>`-tag attributes.
Therefore you can overwrite the method `getRowAttributes`, which depends on a row and returns an `array` with all attributes for this row.
As example, if you want to mark the rows with class *success*, if the row *isFinished*:
```php
<?php
// src/YourBundle/Table/Type/SimpleTableType.php

use JGM\TableBundle\Table\Row\Row;

class SimpleTableType extends AbstractType
{
	// ... some other methods ...
	
	public function getRowAttributes(Row $row)
	{
		if($row->get('isFinished') === true)
		{
			return array('class' => 'success');
		}
		
		return array();
	}
}
```

