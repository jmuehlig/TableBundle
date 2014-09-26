# Create a simple table
Creating tables with this bundle is very easy.

## 1) Create the type
First you have to define a type, comparable to the form type definition.
You have to build the table, set the name and data source.
Optional you can define options and attribute setter for rows, depending on the row itself.

Here is a simple table type:
```php
<?php
// src/YourBundle/Table/Type/SimpleTableType.php

use PZAD\TableBundle\Table\DataSource\DataSourceInterface;
use PZAD\TableBundle\Table\Row\Row;
use PZAD\TableBundle\Table\TableBuilder;
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
	
	public function getRowAttributes(Row $row)
	{
		if($row->getCount() % 2 === 0)
		{
			return array('class' => 'row-gray');
		}
		else
		{
			return array('class' => 'row-white');
		}
	}
}
```

## Add columns
You can add a new columns to your table by calling the method `TableBuilder::add`, using the `buildTable` method.
Syntax of the `add` method is specified as follows: `TableBuilder::add(string $columnType, string $columnName[, array $options])`.
Each column brings is own options, here are the pre-defined columns:

### text
### number
### date
### entity
### content
### counter
